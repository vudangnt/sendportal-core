<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Sendportal\Base\Support\SkillName;
use PHPUnit\Framework\TestCase;

class SkillNameTest extends TestCase
{
    /** @test */
    public function it_does_not_split_a_comma_inside_parentheses()
    {
        self::assertSame(
            ['Tin học văn phòng (Word, Excel, PowerPoint)'],
            SkillName::splitList('Tin học văn phòng (Word, Excel, PowerPoint)')
        );
    }

    /** @test */
    public function it_still_splits_commas_outside_parentheses()
    {
        self::assertSame(['PHP', 'Laravel', 'MySQL'], SkillName::splitList('PHP, Laravel, MySQL'));
        self::assertSame(['AWS (EC2, S3)', 'Docker'], SkillName::splitList('AWS (EC2, S3), Docker'));
    }

    /** @test */
    public function it_drops_blank_segments()
    {
        self::assertSame(['PHP', 'Laravel'], SkillName::splitList(' PHP ,, Laravel '));
    }

    /** @test */
    public function it_repairs_names_broken_by_the_old_splitter()
    {
        self::assertSame('Tin học văn phòng', SkillName::canonical('Tin học văn phòng (Word'));
        self::assertSame('Microsoft Excel', SkillName::canonical('Microsoft Excel (VLOOKUP'));
        self::assertSame('PowerPoint', SkillName::canonical('PowerPoint)'));
    }

    /** @test */
    public function it_leaves_well_formed_names_alone()
    {
        self::assertSame('AWS (EC2, S3)', SkillName::canonical('AWS (EC2, S3)'));
        self::assertSame('Excel', SkillName::canonical('Excel'));
    }

    /** @test */
    public function it_can_tell_an_intact_name_from_a_damaged_one()
    {
        self::assertTrue(SkillName::isWellFormed('PowerPoint'));
        self::assertTrue(SkillName::isWellFormed('AWS (EC2, S3)'));
        self::assertFalse(SkillName::isWellFormed('PowerPoint)'));
        self::assertFalse(SkillName::isWellFormed('Tin học văn phòng (Word'));
    }

    /** @test */
    public function it_groups_punctuation_variants_of_the_same_skill()
    {
        $key = SkillName::groupKey('Agile/Scrum');
        self::assertSame($key, SkillName::groupKey('Agile / Scrum'));
        self::assertSame($key, SkillName::groupKey('Agile & Scrum'));

        self::assertSame(SkillName::groupKey('C#/.NET'), SkillName::groupKey('C# .NET'));
        self::assertSame(SkillName::groupKey('PowerPoint'), SkillName::groupKey('PowerPoint)'));
    }

    /** @test */
    public function it_keeps_skills_that_only_differ_by_a_symbol_apart()
    {
        $c = SkillName::groupKey('C');
        $sharp = SkillName::groupKey('C#');
        $plus = SkillName::groupKey('C++');

        self::assertNotSame($c, $sharp);
        self::assertNotSame($c, $plus);
        self::assertNotSame($sharp, $plus);
    }

    /** @test */
    public function it_keeps_non_latin_names_addressable()
    {
        self::assertNotSame('', SkillName::groupKey('日本語'));
        self::assertNotSame(SkillName::groupKey('日本語'), SkillName::groupKey('한국어'));
    }
}
