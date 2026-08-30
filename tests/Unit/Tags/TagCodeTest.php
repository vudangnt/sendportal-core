<?php

declare(strict_types=1);

namespace Tests\Unit\Tags;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Sendportal\Base\Tags\TagCode;

class TagCodeTest extends TestCase
{
    /** @test */
    public function it_strips_vietnamese_diacritics_and_uppercases(): void
    {
        $this->assertSame('LOC_HO_CHI_MINH', TagCode::make('LOC', 'Hồ Chí Minh')->code);
        $this->assertSame('LOC_DA_NANG', TagCode::make('LOC', 'Đà Nẵng')->code);
    }

    /** @test */
    public function it_collapses_punctuation_and_repeated_separators(): void
    {
        $this->assertSame('CAT_DATA_ANALYTICS', TagCode::make('CAT', 'Data / Analytics')->code);
        $this->assertSame('SKILL_NODE_JS', TagCode::make('SKILL', 'Node.js')->code);
    }

    /** @test */
    public function it_keeps_skills_that_only_differ_by_a_symbol_apart(): void
    {
        // Chuan hoa tho cho C, C# va C++ CUNG ra SKILL_C — gop nham hai ngon ngu.
        $this->assertSame('SKILL_C', TagCode::make('SKILL', 'C')->code);
        $this->assertSame('SKILL_C_SHARP', TagCode::make('SKILL', 'C#')->code);
        $this->assertSame('SKILL_C_PLUS_PLUS', TagCode::make('SKILL', 'C++')->code);
    }

    /** @test */
    public function it_keeps_a_non_latin_name_addressable(): void
    {
        // Ten toan chu khong-Latin bi [^A-Z0-9] xoa sach -> phai co ma du phong on dinh.
        $code = TagCode::make('SKILL', '日本語')->code;
        $this->assertNotSame('SKILL_', $code);
        $this->assertSame($code, TagCode::make('SKILL', '日本語')->code);
    }

    /** @test */
    public function it_trims_leading_and_trailing_separators(): void
    {
        $this->assertSame('IND_TECH', TagCode::make('IND', '  --tech--  ')->code);
    }

    /** @test */
    public function it_is_idempotent_when_the_name_already_looks_like_a_code(): void
    {
        // Đồng bộ chạy lại nhiều lần không được sinh LOC_LOC_HCM.
        $this->assertSame('LOC_HCM', TagCode::make('LOC', 'LOC_HCM')->code);
    }

    /** @test */
    public function it_rejects_an_unknown_dimension(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TagCode::make('KHONG_CO', 'x');
    }

    /** @test */
    public function it_rejects_a_name_that_normalises_to_nothing(): void
    {
        // "???" không còn ký tự nào → tạo tag rác. Phải ném lỗi để caller đưa vào hàng chờ.
        $this->expectException(InvalidArgumentException::class);
        TagCode::make('LOC', '???');
    }

    /** @test */
    public function it_truncates_to_fit_the_column(): void
    {
        $code = TagCode::make('SKILL', str_repeat('a', 200))->code;
        $this->assertLessThanOrEqual(64, strlen($code));
    }
}
