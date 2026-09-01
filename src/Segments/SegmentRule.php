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
 * HỢP ĐỒNG CHUẨN HOÁ DIMENSION — bên SQL (bộ resolver sau) PHẢI khớp Y HỆT:
 *   PHP:  strtoupper(trim((string) ($tag->dimension ?? ''))), rỗng => NHOM_KHAC
 *   SQL:  COALESCE(NULLIF(TRIM(UPPER(t.dimension)), ''), '_KHAC')
 * Lệch chuẩn hoá (vd PHP giữ NULL và '' là 2 nhóm trong khi SQL DISTINCT gộp
 * làm 1; hoặc PHP phân biệt hoa/thường trong khi collation MySQL mặc định
 * không) làm COUNT(DISTINCT ...) trong HAVING không bao giờ khớp
 * dimensionCount() — rule âm thầm chọn ZERO người nhận.
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
        return array_values(array_unique(array_merge([], ...array_values($this->groups))));
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
