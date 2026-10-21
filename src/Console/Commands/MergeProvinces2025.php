<?php

declare(strict_types=1);

namespace Sendportal\Base\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Sendportal\Base\Locations\ProvinceMergeMap;
use Sendportal\Base\Locations\ProvinceMergePlanner;
use Throwable;

/**
 * Ghép location 63 -> 34 tỉnh/thành (NQ 202/2025/QH15) cho 1 workspace: chuyển subscriber từ dòng
 * location tỉnh cũ sang dòng giữ lại. KHÔNG xoá dòng cũ (còn 0 subscriber), KHÔNG đụng
 * campaign_location và tag LOC.
 *
 * Mặc định dry-run. --apply sao lưu vào <bảng>_bak_<Ymd> rồi ghi trong transaction; idempotent.
 * --restore=Ymd phục hồi tên, pivot và subscribers.location_id của các location bị ảnh hưởng.
 */
class MergeProvinces2025 extends Command
{
    private const MOVE_CHUNK = 500;

    protected $signature = 'sp:locations:merge-2025
                            {--workspace= : workspace_id (bắt buộc)}
                            {--map= : đường dẫn file JSON bảng ghép xuất từ RC}
                            {--apply : ghi thật (mặc định dry-run)}
                            {--restore= : phục hồi từ bản sao lưu Ymd}';

    protected $description = 'Ghép location tỉnh/thành 63 -> 34 (dry-run mặc định; --apply; --restore=Ymd)';

    public function handle(): int
    {
        $workspaceId = (int) $this->option('workspace');
        if ($workspaceId <= 0) {
            $this->error('Cần --workspace.');

            return 1;
        }

        $restore = $this->option('restore');
        if ($restore !== null && $restore !== '') {
            return $this->restore($workspaceId, (string) $restore);
        }

        try {
            $map = ProvinceMergeMap::fromFile((string) $this->option('map'));
        } catch (InvalidArgumentException $e) {
            $this->error('Cần --map hợp lệ: ' . $e->getMessage());

            return 1;
        }

        $plan = (new ProvinceMergePlanner($map))->build($workspaceId);
        $apply = (bool) $this->option('apply');

        $this->report($plan, $apply);

        if (! ProvinceMergePlanner::hasChanges($plan)) {
            $this->info('Không có gì để thay đổi.');

            return 0;
        }
        if (! $apply) {
            $this->comment('Dry-run: chưa ghi gì. Thêm --apply để ghi.');

            return 0;
        }

        try {
            $suffix = date('Ymd');
            $this->backup($workspaceId, $plan, $suffix);
            $this->applyPlan($plan);
        } catch (Throwable $e) {
            $this->error('Lỗi, đã rollback phần ghi: ' . $e->getMessage());

            return 1;
        }
        $this->info("Đã áp dụng. Sao lưu: *_bak_{$suffix}. Phục hồi: --restore={$suffix}");

        return 0;
    }

    private function report(array $plan, bool $apply): void
    {
        $this->line($apply ? '== APPLY ==' : '== DRY-RUN (không ghi gì) ==');

        if ($plan['renames'] !== []) {
            $this->line('Đổi tên dòng giữ:');
            $this->table(['id', 'từ', 'thành'], array_map(function ($r) {
                return [$r['id'], $r['from'], $r['to']];
            }, $plan['renames']));
        }

        $this->line('Bảng ghép (dòng cũ -> dòng giữ):');
        $this->table(['id cũ', 'tên cũ', 'sub', '->', 'id giữ', 'tên giữ', 'chuyển', 'trùng bỏ', 'sub.location_id'],
            array_map(function ($m) {
                return [$m['from']['id'], $m['from']['name'], $m['from']['subs'], '->', $m['to']['id'], $m['to']['name'],
                    count($m['move']), count($m['drop']), $m['subscriberRows']];
            }, $plan['merges']));

        $this->line('Dòng biến thể giữ nguyên (chuẩn hoá trùng tên mới nhưng không bị ghép; người quyết định sau):');
        $this->table(['id', 'tên', 'sub', 'tỉnh mới'], array_map(function ($v) {
            return [$v['id'], $v['name'], $v['subs'], $v['province']];
        }, $plan['variants']));

        if ($plan['skipped'] !== []) {
            $this->line('Bỏ qua:');
            $this->table(['id', 'tên', 'sub', 'tỉnh mới', 'lý do'], array_map(function ($s) {
                return [$s['id'], $s['name'], $s['subs'], $s['province'], $s['reason']];
            }, $plan['skipped']));
        }

        $this->line('Tên chưa nhận ra (để nguyên):');
        $this->table(['id', 'tên', 'sub'], array_map(function ($u) {
            return [$u['id'], $u['name'], $u['subs']];
        }, $plan['unrecognized']));

        $move = $drop = $rows = 0;
        foreach ($plan['merges'] as $m) {
            $move += count($m['move']);
            $drop += count($m['drop']);
            $rows += $m['subscriberRows'];
        }
        $this->line("pivot cần chuyển: {$move}");
        $this->line("pivot trùng sẽ bỏ: {$drop}");
        $this->line("subscribers.location_id cần đổi: {$rows}");
        $this->line('campaign_location tham chiếu: ' . $plan['campaignRefs'] . ' (không đụng)');
    }

    /** Sao lưu các dòng sắp bị đổi vào <bảng>_bak_<Ymd> (INSERT IGNORE nên bản đầu trong ngày được giữ). */
    private function backup(int $workspaceId, array $plan, string $suffix): void
    {
        $mergedIds = array_map(function ($m) {
            return (int) $m['from']['id'];
        }, $plan['merges']);
        $renamedIds = array_map(function ($r) {
            return (int) $r['id'];
        }, $plan['renames']);
        $keptIds = array_map('intval', $plan['keptIds']);

        $locIds = implode(',', array_unique(array_merge($mergedIds, $renamedIds, $keptIds)));
        $pivotIds = implode(',', array_unique(array_merge($mergedIds, $keptIds)));
        $mergedList = $mergedIds === [] ? '0' : implode(',', $mergedIds);

        [$loc, $piv, $sub] = $this->backupTables($suffix);

        DB::statement("CREATE TABLE IF NOT EXISTS `{$loc}` LIKE `sendportal_locations`");
        DB::statement("CREATE TABLE IF NOT EXISTS `{$piv}` LIKE `sendportal_location_subscriber`");
        DB::statement("CREATE TABLE IF NOT EXISTS `{$sub}` (id BIGINT UNSIGNED NOT NULL PRIMARY KEY, location_id BIGINT UNSIGNED NULL)");

        DB::statement("INSERT IGNORE INTO `{$loc}` SELECT * FROM `sendportal_locations` WHERE workspace_id = {$workspaceId} AND id IN ({$locIds})");
        DB::statement("INSERT IGNORE INTO `{$piv}` SELECT * FROM `sendportal_location_subscriber` WHERE location_id IN ({$pivotIds})");
        DB::statement("INSERT IGNORE INTO `{$sub}` (id, location_id) SELECT id, location_id FROM `sendportal_subscribers` WHERE location_id IN ({$mergedList})");
    }

    private function applyPlan(array $plan): void
    {
        DB::transaction(function () use ($plan) {
            foreach ($plan['renames'] as $r) {
                DB::table('sendportal_locations')->where('id', $r['id'])->update(['name' => $r['to']]);
            }

            $now = now();
            foreach ($plan['merges'] as $m) {
                $from = $m['from']['id'];
                $to = $m['to']['id'];

                foreach (array_chunk($m['move'], self::MOVE_CHUNK) as $chunk) {
                    DB::table('sendportal_location_subscriber')->insert(array_map(function ($sid) use ($to, $now) {
                        return ['location_id' => $to, 'subscriber_id' => $sid, 'created_at' => $now, 'updated_at' => $now];
                    }, $chunk));
                }
                DB::table('sendportal_location_subscriber')->where('location_id', $from)->delete();
                DB::table('sendportal_subscribers')->where('location_id', $from)->update(['location_id' => $to]);
            }
        });
    }

    private function restore(int $workspaceId, string $suffix): int
    {
        if (! preg_match('/^\d{8}$/', $suffix)) {
            $this->error('--restore phải dạng Ymd (8 chữ số).');

            return 1;
        }
        [$loc, $piv, $sub] = $this->backupTables($suffix);
        foreach ([$loc, $piv, $sub] as $table) {
            if (! Schema::hasTable($table)) {
                $this->error("Không có bảng sao lưu {$table}.");

                return 1;
            }
        }

        try {
            DB::transaction(function () use ($loc, $piv, $sub, $workspaceId) {
                DB::statement("UPDATE `sendportal_locations` l JOIN `{$loc}` b ON b.id = l.id SET l.name = b.name WHERE b.workspace_id = {$workspaceId}");
                DB::statement("DELETE FROM `sendportal_location_subscriber` WHERE location_id IN (SELECT id FROM `{$loc}` WHERE workspace_id = {$workspaceId})");
                DB::statement("INSERT INTO `sendportal_location_subscriber` SELECT * FROM `{$piv}` WHERE location_id IN (SELECT id FROM `{$loc}` WHERE workspace_id = {$workspaceId})");
                DB::statement("UPDATE `sendportal_subscribers` s JOIN `{$sub}` b ON b.id = s.id SET s.location_id = b.location_id");
            });
        } catch (Throwable $e) {
            $this->error('Phục hồi lỗi, đã rollback: ' . $e->getMessage());

            return 1;
        }
        $this->info("Đã phục hồi từ *_bak_{$suffix}.");

        return 0;
    }

    /** @return array{0:string,1:string,2:string} */
    private function backupTables(string $suffix): array
    {
        return [
            "sendportal_locations_bak_{$suffix}",
            "sendportal_location_subscriber_bak_{$suffix}",
            "sendportal_subscribers_bak_{$suffix}",
        ];
    }
}
