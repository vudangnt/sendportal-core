<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use Sendportal\Base\Models\TransactionalSource;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionalSourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_generates_a_uuid_hash_on_create()
    {
        $source = TransactionalSource::create([
            'workspace_id' => 1,
            'request_payload' => ['from' => 'a@b.com'],
        ]);

        $this->assertNotEmpty($source->hash);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            $source->hash
        );
    }

    /** @test */
    public function it_casts_request_payload_to_array()
    {
        $source = TransactionalSource::create([
            'workspace_id' => 1,
            'request_payload' => ['from' => 'a@b.com'],
        ]);

        $this->assertIsArray($source->fresh()->request_payload);
        $this->assertEquals('a@b.com', $source->fresh()->request_payload['from']);
    }
}
