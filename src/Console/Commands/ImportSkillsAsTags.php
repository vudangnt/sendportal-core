<?php

declare(strict_types=1);

namespace Sendportal\Base\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Sendportal\Base\Models\Tag;
use Sendportal\Base\Tags\TagCodeResolver;

/**
 * Nhân bản sendportal_skills → sendportal_tags (dimension SKILL) và pivot subscriber.
 *
 * Pha 2 (segment engine boolean) cần truy vấn MỘT pivot; user chốt giữ target theo skill
 * nên skill phải có mặt trong namespace tag.
 *
 * ĐIỀU KIỆN TIÊN QUYẾT: chạy `sp:skills:group` trước. Ingest cũ cắt tên bằng
 * explode(',') không đếm ngoặc nên đã băm 738 tên chuẩn có dấu phẩy trong ngoặc
 * thành nhiều skill giả; chạy task này trước là đóng đinh rác vào taxonomy mới.
 *
 * Idempotent — chạy lại không nhân đôi. KHÔNG xoá bảng cũ: UI campaign vẫn đọc nó.
 */
class ImportSkillsAsTags extends Command
{
    protected $signature = 'sp:tags:import-skills
                            {--workspace= : workspace_id cần xử lý}
                            {--min-subscribers=3 : bỏ qua skill có ít hơn N subscriber}
                            {--chunk=500 : kích thước lô}
                            {--dry-run : chỉ in ra, không ghi}';

    protected $description = 'Nhân bản skill sang namespace tag (dimension SKILL)';

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

        // Bỏ đuôi dài: 65% skill có 0-1 subscriber (rác từ parse CV), không dùng target được.
        $minSubscribers = max(1, (int) $this->option('min-subscribers'));

        DB::table('sendportal_skills')
            ->where('workspace_id', $workspaceId)
            ->whereRaw('(SELECT COUNT(*) FROM sendportal_skill_subscriber ss WHERE ss.skill_id = sendportal_skills.id) >= ?', [$minSubscribers])
            ->orderBy('id')
            ->chunk((int) $this->option('chunk'), function ($skills) use (
                $resolver, $workspaceId, $dryRun, &$tagMoi, &$pivotMoi, &$khongChuanHoaDuoc
            ) {
                foreach ($skills as $skill) {
                    $code = $resolver->resolve($workspaceId, 'SKILL', (string) $skill->name);

                    if ($code === null) {
                        $khongChuanHoaDuoc++;
                        continue;
                    }

                    if ($dryRun) {
                        $this->line("  {$skill->name} → {$code}");
                        $tagMoi++;
                        continue;
                    }

                    // KHÔNG dùng firstOrCreate: `workspace_id` cố ý không fillable (chống
                    // rò tenant) nên mass-assignment nuốt mất nó. Xem
                    // BaseTenantRepository::executeSave() — cả repo gán tenant key thẳng.
                    $tag = Tag::where('workspace_id', $workspaceId)->where('code', $code)->first();

                    if ($tag === null) {
                        $tag = new Tag();
                        $tag->fill([
                            'name' => $skill->name,
                            'code' => $code,
                            'dimension' => 'SKILL',
                            'source' => 'import_skill',
                            'parent_id' => 0,
                        ]);
                        $tag->workspace_id = $workspaceId;
                        $tag->save();
                        $tagMoi++;
                    }

                    // Chỉ chèn dòng pivot CHƯA có — chạy lại không nhân đôi.
                    $pivotMoi += DB::affectingStatement('
                        INSERT INTO sendportal_tag_subscriber (tag_id, subscriber_id, created_at, updated_at)
                        SELECT ?, ss.subscriber_id, NOW(), NOW()
                        FROM sendportal_skill_subscriber ss
                        WHERE ss.skill_id = ?
                          AND NOT EXISTS (
                              SELECT 1 FROM sendportal_tag_subscriber ts
                              WHERE ts.tag_id = ? AND ts.subscriber_id = ss.subscriber_id
                          )
                    ', [$tag->id, $skill->id, $tag->id]);
                }
            });

        $this->info(($dryRun ? '[DRY-RUN] ' : '')
            . "Tag mới: {$tagMoi} | Dòng pivot mới: {$pivotMoi} | Không chuẩn hoá được: {$khongChuanHoaDuoc}");

        foreach ($resolver->unresolved() as $ten) {
            $this->warn("  chưa chuẩn hoá được: \"{$ten}\"");
        }

        return 0;
    }
}
