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
     * @throws SegmentRuleMismatchException khi PHP và DB đếm dimension khác nhau
     *
     * Caller PHẢI dùng:  ->chunkById(1000, $fn, 'ts.subscriber_id', 'subscriber_id')
     * KHÔNG dùng chunk(): nó phân trang bằng LIMIT/OFFSET, ai đó huỷ đăng ký giữa chừng
     * là lệch offset và BỎ SÓT người nhận, im lặng. chunkById đúng ở đây vì
     * ts.subscriber_id chính là khoá GROUP BY. Tham số mặc định KHÔNG chạy được:
     * ba bảng join đều có cột `id` -> lỗi 1052 "Column 'id' in order clause is ambiguous".
     *
     * ĐỪNG thêm orderBy vào query này: chunkById chỉ gỡ order trên đúng cột phân trang,
     * order lạ sẽ sống sót và làm trang sau bỏ sót người nhận.
     */
    public function query(int $workspaceId, SegmentRule $rule): Builder
    {
        if ($rule->isEmpty()) {
            // KHÔNG trả query rỗng: caller dễ hiểu nhầm thành "gửi tất cả" và blast 80k người.
            throw new EmptySegmentRuleException(
                'Campaign chưa chọn tag nào — không xác định được người nhận.'
            );
        }

        // dimensionCount() đến từ object của caller, còn HAVING đếm dimension trong DB.
        // Hai nguồn này CÓ THỂ lệch — caller quên select cột dimension, hoặc dimension có
        // ký tự mà PHP trim() strip còn SQL TRIM() thì không. Khi lệch, rule KHÔNG chọn 0
        // người mà chọn NHẦM người: subscriber đủ số dimension nhờ tag cùng nhóm vẫn lọt.
        // Nên đối chiếu lại bằng chính biểu thức SQL, và dừng hẳn nếu vênh.
        $bieuThuc = "COALESCE(NULLIF(TRIM(UPPER(dimension)), ''), ?)";
        $soTrongDb = (int) DB::table('sendportal_tags')
            ->where('workspace_id', $workspaceId)
            ->whereIn('id', $rule->tagIds())
            ->selectRaw("COUNT(DISTINCT {$bieuThuc}) as n", [SegmentRule::NHOM_KHAC])
            ->value('n');

        if ($soTrongDb !== $rule->dimensionCount()) {
            throw new SegmentRuleMismatchException(sprintf(
                'Số dimension lệch: PHP đếm %d, DB đếm %d. Rule không đáng tin, dừng lại.',
                $rule->dimensionCount(), $soTrongDb
            ));
        }

        return DB::table('sendportal_tag_subscriber as ts')
            ->join('sendportal_tags as t', 't.id', '=', 'ts.tag_id')
            ->join('sendportal_subscribers as s', 's.id', '=', 'ts.subscriber_id')
            ->where('t.workspace_id', $workspaceId)
            ->where('s.workspace_id', $workspaceId)
            ->whereNull('s.unsubscribed_at')
            ->whereIn('t.id', $rule->tagIds())
            ->groupBy('ts.subscriber_id')
            // Phải SOI GƯƠNG đúng chuẩn hoá của SegmentRule::fromTags(). Khi lệch, rule
            // KHÔNG chọn 0 người mà chọn NHẦM người (xem cross-check phía trên, đó là lý
            // do nó tồn tại) — chuẩn hoá dưới đây vẫn phải khớp PHP làm phòng tuyến thứ nhất:
            //   - COALESCE không đủ: tag dimension NULL và tag dimension '' ra 2 distinct
            //     bên SQL nhưng 1 nhóm bên PHP -> người có CẢ HAI tag bị loại.
            //   - UPPER vì collation MySQL mặc định không phân biệt hoa thường: 'cat' và
            //     'CAT' là 1 distinct bên SQL, nên PHP cũng gộp (strtoupper).
            ->havingRaw('COUNT(DISTINCT COALESCE(NULLIF(TRIM(UPPER(t.dimension)), \'\'), ?)) = ?', [
                SegmentRule::NHOM_KHAC,
                $rule->dimensionCount(),
            ])
            ->select('ts.subscriber_id');
    }

    /**
     * fromSub() là belt-and-braces: Laravel đã tự bọc aggregate khi query có `havings`
     * (Grammar::compileAggregate -> compileUnionAggregate), nên query(...)->count() vốn
     * đã ra đúng số nhóm rồi. Giữ wrap thủ công cho rõ ý, không phải vì bắt buộc.
     */
    public function count(int $workspaceId, SegmentRule $rule): int
    {
        return DB::query()->fromSub($this->query($workspaceId, $rule), 'z')->count();
    }
}
