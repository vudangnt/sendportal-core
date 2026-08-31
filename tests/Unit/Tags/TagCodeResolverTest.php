<?php

declare(strict_types=1);

namespace Tests\Unit\Tags;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sendportal\Base\Models\TagAlias;
use Sendportal\Base\Tags\TagCodeResolver;
use Tests\SendportalTestSupportTrait;
use Tests\TestCase;

class TagCodeResolverTest extends TestCase
{
    use RefreshDatabase;
    use SendportalTestSupportTrait;

    private TagCodeResolver $resolver;

    public function setUp(): void
    {
        parent::setUp();
        $this->resolver = app()->make(TagCodeResolver::class);
    }

    /** @test */
    public function alias_wins_over_the_normaliser(): void
    {
        TagAlias::create([
            'workspace_id' => 1, 'dimension' => 'LOC',
            'alias' => 'tp.hcm', 'code' => 'LOC_HCM',
        ]);

        $this->assertSame('LOC_HCM', $this->resolver->resolve(1, 'LOC', 'TP.HCM'));
    }

    /** @test */
    public function alias_lookup_ignores_case_and_surrounding_space(): void
    {
        TagAlias::create([
            'workspace_id' => 1, 'dimension' => 'LOC',
            'alias' => 'sai gon', 'code' => 'LOC_HCM',
        ]);

        $this->assertSame('LOC_HCM', $this->resolver->resolve(1, 'LOC', '  Sai Gon  '));
    }

    /** @test */
    public function it_falls_back_to_the_normaliser_when_no_alias_exists(): void
    {
        $this->assertSame('LOC_HA_NOI', $this->resolver->resolve(1, 'LOC', 'Hà Nội'));
    }

    /** @test */
    public function alias_of_another_workspace_is_not_used(): void
    {
        TagAlias::create([
            'workspace_id' => 2, 'dimension' => 'LOC',
            'alias' => 'tp.hcm', 'code' => 'LOC_HCM',
        ]);

        // workspace 1 không có alias → phải rơi về normalizer, KHÔNG mượn của workspace 2.
        $this->assertSame('LOC_TP_HCM', $this->resolver->resolve(1, 'LOC', 'TP.HCM'));
    }

    /** @test */
    public function unresolvable_names_return_null_and_are_recorded(): void
    {
        $this->assertNull($this->resolver->resolve(1, 'LOC', '???'));
        $this->assertContains('???', $this->resolver->unresolved());
    }
}
