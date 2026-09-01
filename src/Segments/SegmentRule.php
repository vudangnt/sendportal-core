<?php

declare(strict_types=1);

namespace Sendportal\Base\Segments;

/**
 * Tập tag mà một campaign nhắm tới, đã gom theo dimension.
 *
 * Ngữ nghĩa (tài liệu Lark CbyywcnNGiIMD8kUv7HlaElCgSf §8): cùng dimension là OR,
 * khác dimension là AND. Marketer tick tag theo tab, không phải gõ cú pháp.
 *
 * Không chạm DB, không phụ thuộc container — để test được không cần harness.
 *
 * CHUẨN HOÁ DIMENSION — bên SQL (bộ resolver sau) dùng:
 *   PHP:  strtoupper(trim((string) ($tag->dimension ?? ''))), rỗng => NHOM_KHAC
 *   SQL:  COALESCE(NULLIF(TRIM(UPPER(t.dimension)), ''), '_KHAC')
 * Điều kiện: `dimension` chỉ nhận giá trị trong Dimension::ALL — chữ HOA ASCII, không
 * khoảng trắng, không dấu. Trong phạm vi đó, chuẩn hoá PHP và SQL cho cùng kết quả.
 * NGOÀI phạm vi đó chúng LỆCH nhau và rule âm thầm chọn sai người:
 *   'CAT' vs 'CÁT'   -> PHP 2 nhóm, SQL 1 distinct  -> không khớp ai
 *   "CAT\n" vs 'CAT' -> PHP 1 nhóm, SQL 2 distinct  -> mất bớt người
 * strtoupper là ASCII-only, còn UPPER của MySQL hiểu Unicode; trim() của PHP strip
 * \n\t\r còn TRIM() của SQL chỉ strip dấu cách. Chốt chặn đúng chỗ là validate lúc
 * GHI tag, không phải lúc đọc.
 */
final class SegmentRule
{
    /** Nhóm cho tag chưa có dimension. Prod còn 10 tag như vậy (trùng mã nên backfill bỏ qua). */
    public const NHOM_KHAC = '_KHAC';

    /** @param array<string, int[]> $groups */
    private function __construct(private readonly array $groups)
    {
    }

    /**
     * @param iterable<object> $tags mỗi phần tử cần có ->id và ->dimension
     */
    public static function fromTags(iterable $tags): self
    {
        $groups = [];
        $seenIds = [];

        foreach ($tags as $tag) {
            $id = (int) $tag->id;

            // Cùng id xuất hiện ở 2 dimension khác nhau sẽ làm dimensionCount
            // vượt số tag thật -> COUNT(DISTINCT) không bao giờ đạt. Dimension
            // gặp trước thắng, các lần sau của cùng id bị bỏ qua hẳn.
            if (isset($seenIds[$id])) {
                continue;
            }

            $dimension = strtoupper(trim((string) ($tag->dimension ?? '')));
            $key = $dimension === '' ? self::NHOM_KHAC : $dimension;

            $groups[$key][] = $id;
            $seenIds[$id] = true;
        }

        return new self($groups);
    }

    /** @return array<string, int[]> */
    public function groups(): array
    {
        return $this->groups;
    }

    /** @return int[] */
    public function tagIds(): array
    {
        return array_merge([], ...array_values($this->groups));
    }

    /** Số dimension — đây mới là con số cho HAVING, KHÔNG phải số tag. */
    public function dimensionCount(): int
    {
        return count($this->groups);
    }

    public function isEmpty(): bool
    {
        return $this->groups === [];
    }
}
