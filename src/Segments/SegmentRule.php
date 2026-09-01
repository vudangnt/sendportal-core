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

        foreach ($tags as $tag) {
            $dimension = trim((string) ($tag->dimension ?? ''));
            $key = $dimension === '' ? self::NHOM_KHAC : $dimension;
            $id = (int) $tag->id;

            if (! in_array($id, $groups[$key] ?? [], true)) {
                $groups[$key][] = $id;
            }
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
        return array_values(array_unique(array_merge(...array_values($this->groups) ?: [[]])));
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
