<?php

declare(strict_types=1);

namespace Sendportal\Base\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Sendportal\Base\Models\Tag;

/**
 * Đo sức khoẻ taxonomy. Ba con số quan trọng:
 *  - Chưa có mã: còn bao nhiêu tag chưa backfill.
 *  - Trùng tên (khác hoa/thường/dấu): ứng viên cần gộp tay.
 *  - AUD_UNKNOWN: bao nhiêu subscriber chưa biết audience → còn producer chưa deploy.
 */
class TagTaxonomyReport extends Command
{
    protected $signature = 'sp:tags:report {--workspace= : workspace_id}';

    protected $description = 'Báo cáo tag chưa có mã / trùng tên / AUD_UNKNOWN';

    public function handle(): int
    {
        $workspaceId = (int) $this->option('workspace');

        if ($workspaceId <= 0) {
            $this->error('Cần --workspace.');

            return 1;
        }

        $chuaCoMa = Tag::where('workspace_id', $workspaceId)->whereNull('code')->count();
        $this->line("Chưa có mã: {$chuaCoMa}");

        $trungTen = DB::table('sendportal_tags')
            ->selectRaw('LOWER(TRIM(name)) k, COUNT(*) n')
            ->where('workspace_id', $workspaceId)
            ->groupBy('k')
            ->havingRaw('COUNT(*) > 1')
            ->get();
        $this->line("Trùng tên (cần gộp tay): {$trungTen->count()}");
        foreach ($trungTen->take(20) as $r) {
            $this->warn("  \"{$r->k}\" × {$r->n}");
        }

        // Tên không thuộc bảng Latin rơi vào mã dự phòng dạng <DIM>_X<hash> —
        // addressable nhưng người đọc không hiểu, phải đặt lại tên bằng tay.
        $maBam = DB::table('sendportal_tags')
            ->where('workspace_id', $workspaceId)
            ->whereNotNull('code')
            ->where('code', 'REGEXP', '_X[0-9A-F]{10}$')
            ->pluck('name', 'code');
        $this->line("Mã dự phòng không đọc được (cần đặt lại tên): {$maBam->count()}");
        foreach ($maBam as $ma => $ten) {
            $this->warn("  {$ma}  <-  {$ten}");
        }

        $unknown = Tag::where('workspace_id', $workspaceId)->where('code', 'AUD_UNKNOWN')->first();
        $soUnknown = $unknown ? $unknown->subscribers()->count() : 0;
        $this->line("AUD_UNKNOWN (subscriber chưa biết audience): {$soUnknown}");

        // DB::table chứ không phải model Tag: Tag có $withCount nên Eloquent chèn
        // `sendportal_tags.*` + hai subquery đếm subscriber vào SELECT, phá
        // ONLY_FULL_GROUP_BY và ném lỗi 1055.
        $theoDimension = DB::table('sendportal_tags')
            ->where('workspace_id', $workspaceId)
            ->whereNotNull('dimension')
            ->selectRaw('dimension, COUNT(*) n')
            ->groupBy('dimension')
            ->pluck('n', 'dimension');
        foreach ($theoDimension as $dim => $n) {
            $this->line("  {$dim}: {$n}");
        }

        return 0;
    }
}
