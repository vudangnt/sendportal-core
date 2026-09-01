<?php

declare(strict_types=1);

namespace Sendportal\Base\Segments;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Đổi một SegmentRule thành truy vấn subscriber.
 *
 * "OR trong dimension, AND giữa dimension" ra tự nhiên từ GROUP BY + HAVING: lấy mọi
 * subscriber có ít nhất một tag đã tick, rồi giữ lại người phủ ĐỦ số dimension.
 *
 * Lọc bằng SQL chứ không nạp về PHP: campaign nhắm dimension SKILL có tới 2.251 tag,
 * hợp của chúng quá lớn để lọc trong bộ nhớ.
 */
class SegmentResolver
{
    /**
     * @return Builder truy vấn trả về cột `subscriber_id`
     * @throws EmptySegmentRuleException khi rule rỗng
     */
    public function query(int $workspaceId, SegmentRule $rule): Builder
    {
        if ($rule->isEmpty()) {
            // KHÔNG trả query rỗng: caller dễ hiểu nhầm thành "gửi tất cả" và blast 80k người.
            throw new EmptySegmentRuleException(
                'Campaign chưa chọn tag nào — không xác định được người nhận.'
            );
        }

        return DB::table('sendportal_tag_subscriber as ts')
            ->join('sendportal_tags as t', 't.id', '=', 'ts.tag_id')
            ->join('sendportal_subscribers as s', 's.id', '=', 'ts.subscriber_id')
            ->where('t.workspace_id', $workspaceId)
            ->where('s.workspace_id', $workspaceId)
            ->whereNull('s.unsubscribed_at')
            ->whereIn('t.id', $rule->tagIds())
            ->groupBy('ts.subscriber_id')
            // Phải SOI GƯƠNG đúng chuẩn hoá của SegmentRule::fromTags(), nếu không hai bên
            // đếm khác nhau và rule âm thầm chọn 0 người:
            //   - COALESCE không đủ: tag dimension NULL và tag dimension '' ra 2 distinct
            //     bên SQL nhưng 1 nhóm bên PHP -> người có CẢ HAI tag bị loại.
            //   - UPPER vì collation MySQL mặc định không phân biệt hoa thường: 'cat' và
            //     'CAT' là 1 distinct bên SQL, nên PHP cũng gộp (strtoupper).
            ->havingRaw("COUNT(DISTINCT COALESCE(NULLIF(TRIM(UPPER(t.dimension)), ''), '"
                . SegmentRule::NHOM_KHAC . "')) = ?", [$rule->dimensionCount()])
            ->select('ts.subscriber_id');
    }

    public function count(int $workspaceId, SegmentRule $rule): int
    {
        return DB::query()->fromSub($this->query($workspaceId, $rule), 'z')->count();
    }
}
