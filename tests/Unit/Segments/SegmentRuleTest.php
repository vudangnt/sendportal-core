<?php

declare(strict_types=1);

namespace Tests\Unit\Segments;

use PHPUnit\Framework\TestCase;
use Sendportal\Base\Segments\SegmentRule;

class SegmentRuleTest extends TestCase
{
    /** Tag giả — SegmentRule chỉ đọc id và dimension nên không cần Eloquent. */
    private function tag(int $id, ?string $dimension): object
    {
        return (object) ['id' => $id, 'dimension' => $dimension];
    }

    /** @test */
    public function it_groups_tag_ids_by_dimension(): void
    {
        $rule = SegmentRule::fromTags([
            $this->tag(1, 'CAT'), $this->tag(2, 'CAT'), $this->tag(3, 'LOC'),
        ]);

        self::assertSame(['CAT' => [1, 2], 'LOC' => [3]], $rule->groups());
    }

    /** @test */
    public function it_counts_dimensions_not_tags(): void
    {
        // 3 tag nhưng 2 dimension -> HAVING phải so với 2.
        $rule = SegmentRule::fromTags([
            $this->tag(1, 'CAT'), $this->tag(2, 'CAT'), $this->tag(3, 'LOC'),
        ]);

        self::assertSame(2, $rule->dimensionCount());
        self::assertSame([1, 2, 3], $rule->tagIds());
    }

    /** @test */
    public function it_puts_tags_without_a_dimension_in_their_own_group(): void
    {
        // 10 tag trên prod có dimension NULL. Chúng phải thành MỘT nhóm riêng,
        // không được biến mất — nếu mất thì rule dính tab "Khác" không khớp ai.
        // Dùng literal '_KHAC' (không phải hằng số) vì giá trị này là một phần
        // hợp đồng đã tài liệu hoá — SQL viết tay cho báo cáo có thể hard-code
        // thẳng chuỗi này. Nếu ai đổi giá trị hằng số mà quên cập nhật chỗ đó,
        // test này phải đỏ để lộ ra, không được xanh ăn theo hằng số đã đổi.
        $rule = SegmentRule::fromTags([$this->tag(1, 'CAT'), $this->tag(2, null), $this->tag(3, '')]);

        self::assertSame(['CAT' => [1], '_KHAC' => [2, 3]], $rule->groups());
        self::assertSame(2, $rule->dimensionCount());
    }

    /** @test */
    public function it_defines_nhom_khac_as_the_documented_literal_value(): void
    {
        self::assertSame('_KHAC', SegmentRule::NHOM_KHAC);
    }

    /** @test */
    public function it_is_empty_when_no_tags_are_selected(): void
    {
        $rule = SegmentRule::fromTags([]);

        self::assertTrue($rule->isEmpty());
        self::assertSame(0, $rule->dimensionCount());
        self::assertSame([], $rule->tagIds());
    }

    /** @test */
    public function it_drops_duplicate_tag_ids(): void
    {
        $rule = SegmentRule::fromTags([$this->tag(1, 'CAT'), $this->tag(1, 'CAT')]);

        self::assertSame(['CAT' => [1]], $rule->groups());
    }

    /** @test */
    public function it_keeps_a_tag_id_in_only_one_group(): void
    {
        // Cùng id ở hai dimension sẽ làm dimensionCount vượt số tag thật
        // -> COUNT(DISTINCT) không bao giờ đạt, rule không khớp một ai.
        $rule = SegmentRule::fromTags([$this->tag(30, 'CAT'), $this->tag(30, 'LOC')]);

        self::assertSame(['CAT' => [30]], $rule->groups());
        self::assertSame(1, $rule->dimensionCount());
        self::assertSame([30], $rule->tagIds());
    }

    /** @test */
    public function it_normalises_case_and_padding_so_php_agrees_with_sql(): void
    {
        // MySQL gộp 'cat' với 'CAT' (collation không phân biệt hoa thường) và TRIM
        // khoảng trắng. PHP phải gộp y hệt, nếu không COUNT(DISTINCT) bên SQL và
        // dimensionCount() bên PHP lệch nhau -> HAVING không bao giờ đạt -> rule
        // âm thầm không khớp một ai.
        $rule = SegmentRule::fromTags([$this->tag(1, 'cat'), $this->tag(2, ' CAT ')]);

        self::assertSame(['CAT' => [1, 2]], $rule->groups());
        self::assertSame(1, $rule->dimensionCount());
    }

    /** @test */
    public function it_treats_a_whitespace_only_dimension_as_no_dimension(): void
    {
        $rule = SegmentRule::fromTags([$this->tag(1, '   ')]);

        self::assertSame(['_KHAC' => [1]], $rule->groups());
    }
}
