<?php

declare(strict_types=1);

namespace Tests\Unit\Locations;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Sendportal\Base\Locations\ProvinceMergeMap;

class ProvinceMergeMapTest extends TestCase
{
    private function map(): ProvinceMergeMap
    {
        return ProvinceMergeMap::fromFile(__DIR__ . '/../../fixtures/provinces-2025.json');
    }

    /** @test */
    public function it_resolves_old_names_aliases_and_prefixed_forms(): void
    {
        $map = $this->map();

        $this->assertSame('Hồ Chí Minh', $map->resolve('Bình Dương'));
        $this->assertSame('Hồ Chí Minh', $map->resolve('Tỉnh Bình Dương'));
        $this->assertSame('Hồ Chí Minh', $map->resolve('Vũng tàu'));
        $this->assertSame('Hồ Chí Minh', $map->resolve('TP. Hồ Chí Minh'));
        $this->assertSame('Huế', $map->resolve('Thừa Thiên Huế'));
        $this->assertSame('Phú Thọ', $map->resolve('Hòa Bình'));
        $this->assertSame('Phú Thọ', $map->resolve('Hoà Bình'));
        $this->assertSame('Khánh Hòa', $map->resolve('Nha Trang'));
        // Ð (U+00D0, eth) hay bị dùng thay Đ trong văn bản cũ.
        $this->assertSame('Đồng Nai', $map->resolve('Ðồng Nai'));
    }

    /** @test */
    public function it_returns_null_for_unknown_or_partial_names(): void
    {
        $map = $this->map();

        $this->assertNull($map->resolve('Remote'));
        $this->assertNull($map->resolve('Vietnam'));
        $this->assertNull($map->resolve('Hà Nội2'));
        $this->assertNull($map->resolve('Long Thành Đồng Nai'));
        $this->assertNull($map->resolve(''));
        $this->assertNull($map->resolve(null));
    }

    /** @test */
    public function it_flags_only_names_that_disappear_in_the_merge(): void
    {
        $map = $this->map();

        $this->assertTrue($map->isMergedAway('Bình Dương'));
        $this->assertTrue($map->isMergedAway('Tỉnh Bình Dương'));
        $this->assertTrue($map->isMergedAway('Vũng tàu'));
        $this->assertTrue($map->isMergedAway('Đà Lạt'));
        $this->assertFalse($map->isMergedAway('TP. Hồ Chí Minh'));
        $this->assertFalse($map->isMergedAway('Hồ Chí Minh'));
        $this->assertFalse($map->isMergedAway('Thừa Thiên Huế'));
        $this->assertFalse($map->isMergedAway('Huế'));
        $this->assertFalse($map->isMergedAway('Remote'));
        $this->assertFalse($map->isMergedAway(null));
    }

    /** @test */
    public function it_exposes_the_province_entry(): void
    {
        $province = $this->map()->province('Bình Dương');

        $this->assertSame('79', $province['code']);
        $this->assertSame('Hồ Chí Minh', $province['old'][0]);
    }

    /** @test */
    public function it_rejects_missing_or_malformed_files(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ProvinceMergeMap::fromFile('/nonexistent/map.json');
    }

    /** @test */
    public function it_rejects_entries_without_old_names(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ProvinceMergeMap([['code' => '01', 'name' => 'Hà Nội']]);
    }
}
