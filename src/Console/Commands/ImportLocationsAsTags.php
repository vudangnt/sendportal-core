<?php

declare(strict_types=1);

namespace Sendportal\Base\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Sendportal\Base\Models\Tag;
use Sendportal\Base\Tags\TagCodeResolver;

/**
 * Nhân bản sendportal_locations → tag dimension LOC + pivot subscriber.
 *
 * Segment engine coi mọi thứ là tag có dimension; giữ location ở bảng riêng nghĩa là
 * engine phải đặc cách nó mãi. KHÔNG xoá bảng cũ — giao diện cũ và nhánh legacy vẫn đọc.
 *
 * Idempotent: chạy lại không nhân đôi.
 */
class ImportLocationsAsTags extends Command
{
    protected $signature = 'sp:tags:import-locations
                            {--workspace= : workspace_id cần xử lý}
                            {--dry-run : chỉ in ra, không ghi}';

    protected $description = 'Nhân bản location sang namespace tag (dimension LOC)';

    public function handle(TagCodeResolver $resolver): int
    {
        $workspaceId = (int) $this->option('workspace');
        $dryRun = (bool) $this->option('dry-run');

        if ($workspaceId <= 0) {
            $this->error('Cần --workspace.');

            return 1;
        }

        $tagMoi = 0;
        $pivotMoi = 0;
        $khongChuanHoaDuoc = 0;

        foreach (DB::table('sendportal_locations')->where('workspace_id', $workspaceId)->orderBy('id')->get() as $loc) {
            $code = $resolver->resolve($workspaceId, 'LOC', (string) $loc->name);

            if ($code === null) {
                $khongChuanHoaDuoc++;
                continue;
            }

            if ($dryRun) {
                $this->line("  {$loc->name} → {$code}");
                $tagMoi++;
                continue;
            }

            // KHÔNG dùng firstOrCreate: workspace_id không fillable nên mass-assignment
            // nuốt mất nó và insert ném lỗi. Xem BaseTenantRepository::executeSave().
            $tag = Tag::where('workspace_id', $workspaceId)->where('code', $code)->first();

            if ($tag === null) {
                $tag = new Tag();
                $tag->fill([
                    'name' => $loc->name,
                    'code' => $code,
                    'dimension' => 'LOC',
                    'source' => 'import_location',
                    'parent_id' => 0,
                ]);
                $tag->workspace_id = $workspaceId;
                $tag->save();
                $tagMoi++;
            }

            // JOIN sang sendportal_subscribers: pivot có thể còn dòng mồ côi trỏ tới
            // subscriber đã xoá, mà sendportal_tag_subscriber có khoá ngoại sang đó.
            $pivotMoi += DB::affectingStatement('
                INSERT INTO sendportal_tag_subscriber (tag_id, subscriber_id, created_at, updated_at)
                SELECT ?, ls.subscriber_id, NOW(), NOW()
                FROM sendportal_location_subscriber ls
                JOIN sendportal_subscribers s ON s.id = ls.subscriber_id
                WHERE ls.location_id = ?
                  AND NOT EXISTS (
                      SELECT 1 FROM sendportal_tag_subscriber ts
                      WHERE ts.tag_id = ? AND ts.subscriber_id = ls.subscriber_id
                  )
            ', [$tag->id, $loc->id, $tag->id]);
        }

        $this->info(($dryRun ? '[DRY-RUN] ' : '')
            . "Tag mới: {$tagMoi} | Dòng pivot mới: {$pivotMoi} | Không chuẩn hoá được: {$khongChuanHoaDuoc}");

        foreach ($resolver->unresolved() as $ten) {
            $this->warn("  chưa chuẩn hoá được: \"{$ten}\"");
        }

        return 0;
    }
}
