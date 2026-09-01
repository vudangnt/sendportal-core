<?php

declare(strict_types=1);

namespace Sendportal\Base\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Sendportal\Base\Models\Tag;
use Sendportal\Base\Tags\Dimension;
use Sendportal\Base\Tags\TagCodeResolver;

/**
 * Sinh mã cho tag đang có, dimension lấy theo GỐC CÂY (file --map), con thừa kế gốc.
 *
 * Đo prod 2026-08-29: 787 tag là cây TRỘN — ngành nghề (IT→Java), thùng chứa campaign
 * ([2026] Push job), nhóm đối tượng (Clients - Contact). Một cờ --dimension cho cả
 * workspace là sai ngay từ đầu, nên phải có map.
 *
 * Gốc KHÔNG khai trong map → mặc định LIST, và lệnh in ra hết những gì nó tự gán.
 * Đây là chỗ DUY NHẤT lệnh tự quyết, cố ý nới nguyên tắc "không đoán" (spec §8.4.1):
 * prod có 27 gốc kiểu "[2026] Push job" và marketer tạo mới liên tục, bắt liệt kê tay
 * thì map lạc hậu ngay và backfill không bao giờ chạy được. Đoán sai một LIST_ chỉ là
 * nằm nhầm ngăn — thêm một dòng map rồi chạy lại, vì lệnh idempotent.
 *
 * Ngược lại, tên KHÔNG chuẩn hoá được thì resolver trả null và lệnh BỎ QUA, không tự
 * bịa mã — đó mới là chỗ sinh tag rác thật sự.
 */
class BackfillTagCodes extends Command
{
    protected $signature = 'sp:tags:backfill-codes
                            {--workspace= : workspace_id cần xử lý}
                            {--map= : đường dẫn file JSON {root_tag_id: dimension}}
                            {--chunk=500 : kích thước lô}
                            {--remap : sinh lại mã cho cả tag ĐÃ có mã, khi map đổi phân loại}
                            {--dry-run : chỉ in ra, không ghi}';

    protected $description = 'Sinh code/dimension cho sendportal_tags theo map gốc cây';

    public function handle(TagCodeResolver $resolver): int
    {
        $workspaceId = (int) $this->option('workspace');
        $mapPath = (string) $this->option('map');
        $dryRun = (bool) $this->option('dry-run');

        if ($workspaceId <= 0 || ! is_file($mapPath)) {
            $this->error('Cần --workspace hợp lệ và --map trỏ tới file JSON có thật.');

            return 1;
        }

        $map = json_decode((string) file_get_contents($mapPath), true);
        if (! is_array($map)) {
            $this->error('File map không phải JSON hợp lệ.');

            return 1;
        }

        foreach ($map as $rootId => $dimension) {
            if (str_starts_with((string) $rootId, '_')) {
                continue; // khoá chú thích như "_doc"
            }
            if (! Dimension::isValid((string) $dimension)) {
                $this->error("Dimension không hợp lệ trong map: {$rootId} => {$dimension}");

                return 1;
            }
        }

        $daGan = 0;
        $ngoaiMap = [];
        $trung = [];

        // Mặc định chỉ đụng tag chưa có mã. --remap mở cho cả tag đã có mã, dùng khi
        // bổ sung map: ~218 ngành nghề là gốc PHẲNG (không con) nên lần backfill đầu
        // không thừa kế được dimension nào và rơi hết vào LIST.
        Tag::where('workspace_id', $workspaceId)
            ->when(! $this->option('remap'), fn ($q) => $q->whereNull('code'))
            ->chunkById((int) $this->option('chunk'), function ($tags) use (
                $resolver, $workspaceId, $map, $dryRun, &$daGan, &$ngoaiMap, &$trung
            ) {
                foreach ($tags as $tag) {
                    $rootId = (int) $tag->parent_id === 0 ? (int) $tag->id : (int) $tag->parent_id;

                    // Gốc không khai trong map → LIST (thùng chứa vận hành). Ghi lại hết
                    // để người vận hành soi, vì đây là chỗ duy nhất lệnh tự quyết.
                    // Chế độ remap CHỈ áp lại map, không phân loại lại tất cả: tag đã có
                    // dimension do lệnh khác đặt (SKILL từ import-skills, AUD từ ingest)
                    // mà không nằm trong map thì phải để nguyên. Bỏ chốt này, một lần
                    // chạy sẽ đẩy 2.251 tag SKILL và 3 tag AUD về LIST.
                    if ($this->option('remap') && $tag->code !== null && ! isset($map[(string) $rootId])) {
                        continue;
                    }

                    $dimension = $map[(string) $rootId] ?? 'LIST';
                    if (! isset($map[(string) $rootId])) {
                        $ngoaiMap[] = "{$tag->name} (id={$tag->id}, root={$rootId}) → LIST";
                    }

                    $code = $resolver->resolve($workspaceId, $dimension, (string) $tag->name);
                    if ($code === null) {
                        continue; // resolver đã ghi nhận, in ở cuối
                    }

                    // Chạy lại mà mã không đổi thì không ghi — giữ lệnh idempotent.
                    if ($tag->code === $code && $tag->dimension === $dimension) {
                        continue;
                    }

                    if ($dryRun) {
                        $this->line("  {$tag->name} → {$code}");
                        $daGan++;
                        continue;
                    }

                    try {
                        $tag->update(['code' => $code, 'dimension' => $dimension, 'source' => 'backfill']);
                        $daGan++;
                    } catch (QueryException $e) {
                        if ($e->getCode() !== '23000') {   // 23000 = trùng UNIQUE
                            throw $e;
                        }
                        $trung[] = "{$tag->name} → {$code}";
                    }
                }
            });

        $this->info(($dryRun ? '[DRY-RUN] ' : '')
            . "Đã gán: {$daGan} | Tự gán LIST: " . count($ngoaiMap)
            . ' | Trùng mã: ' . count($trung));

        foreach ($ngoaiMap as $t) {
            $this->warn("  tự gán LIST (soi lại nếu sai): {$t}");
        }
        foreach ($resolver->unresolved() as $ten) {
            $this->warn("  chưa chuẩn hoá được: \"{$ten}\"");
        }
        foreach ($trung as $t) {
            $this->warn("  trùng mã (cần gộp tay): {$t}");
        }

        return 0;
    }
}
