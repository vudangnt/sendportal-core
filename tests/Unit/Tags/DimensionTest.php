<?php

declare(strict_types=1);

namespace Tests\Unit\Tags;

use PHPUnit\Framework\TestCase;
use Sendportal\Base\Tags\Dimension;

class DimensionTest extends TestCase
{
    /** @test */
    public function it_knows_valid_dimensions(): void
    {
        $this->assertTrue(Dimension::isValid('LOC'));
        $this->assertTrue(Dimension::isValid('AUD'));
        $this->assertTrue(Dimension::isValid('CSIZE'));
        $this->assertTrue(Dimension::isValid('LIST'));
        $this->assertFalse(Dimension::isValid('KHONG_CO'));
    }

    /** @test */
    public function it_returns_the_prefix_with_underscore(): void
    {
        $this->assertSame('LOC_', Dimension::prefix('LOC'));
        $this->assertSame('REC_LV_', Dimension::prefix('REC_LV'));
    }

    /** @test */
    public function audience_dimension_has_a_fixed_value_set(): void
    {
        $this->assertSame(
            ['AUD_CANDIDATE', 'AUD_EMPLOYER', 'AUD_LEARNER', 'AUD_TALENTHUNTER', 'AUD_UNKNOWN'],
            Dimension::audienceCodes()
        );
    }
}
