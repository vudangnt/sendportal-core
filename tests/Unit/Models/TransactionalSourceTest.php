<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Models\TransactionalSource;
use Tests\TestCase;

class TransactionalSourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure a workspace record exists to satisfy the FK constraint on workspace_id.
        if (Schema::hasTable('workspaces')) {
            DB::table('workspaces')->updateOrInsert(
                ['id' => Sendportal::currentWorkspaceId()],
                ['owner_id' => 1, 'name' => 'Test Workspace', 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /** @test */
    public function it_generates_a_uuid_hash_on_create()
    {
        $source = TransactionalSource::factory()->create([
            'hash' => null,
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
        $source = TransactionalSource::factory()->create([
            'request_payload' => ['from' => 'a@b.com'],
        ]);

        $this->assertIsArray($source->fresh()->request_payload);
        $this->assertEquals('a@b.com', $source->fresh()->request_payload['from']);
    }
}
