<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Sendportal\Base\Jobs\SendTransactionalMessageJob;
use Sendportal\Base\Models\EmailService;
use Sendportal\Base\Models\TransactionalSource;
use Tests\TestCase;

class TransactionalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function send_endpoint_creates_transactional_source_and_queues_jobs(): void
    {
        Bus::fake();

        $workspaceId = 1;

        EmailService::create([
            'workspace_id' => $workspaceId,
            'name' => 'Test Postmark',
            'type_id' => 1,
            'settings' => ['key' => 'test'],
            'sender_domains' => ['company.com'],
            'is_default' => true,
        ]);

        $payload = [
            'from' => ['email' => 'noreply@company.com', 'name' => 'Company'],
            'to' => [['email' => 'candidate@example.com']],
            'subject' => 'Hello',
            'content' => ['type' => 'html', 'html' => '<p>Test body</p>'],
            'tracking' => ['open' => true, 'click' => true],
            'metadata' => ['ref' => 'job-123'],
        ];

        $response = $this->postJson('/api/v1/transactional/send', $payload);

        $response->assertCreated()
            ->assertJsonStructure([
                'transactional_hash',
                'messages' => [
                    ['message_hash', 'recipient'],
                ],
            ]);

        $this->assertDatabaseHas('sendportal_transactional_sources', [
            'workspace_id' => $workspaceId,
        ]);

        Bus::assertDispatched(SendTransactionalMessageJob::class);
    }

    /** @test */
    public function send_endpoint_returns_422_when_no_email_service_for_domain(): void
    {
        $payload = [
            'from' => ['email' => 'noreply@unknown.com'],
            'to' => [['email' => 'test@example.com']],
            'subject' => 'Hi',
            'content' => ['type' => 'html', 'html' => '<p>Hi</p>'],
        ];

        $response = $this->postJson('/api/v1/transactional/send', $payload);

        $response->assertStatus(422);
    }

    /** @test */
    public function show_endpoint_returns_transactional_source_by_hash(): void
    {
        $source = TransactionalSource::create([
            'workspace_id' => 1,
            'request_payload' => ['test' => true],
        ]);

        $response = $this->getJson('/api/v1/transactional/' . $source->hash);

        $response->assertOk()
            ->assertJsonPath('transactional_hash', $source->hash);
    }

    /** @test */
    public function show_endpoint_returns_404_for_unknown_hash(): void
    {
        $response = $this->getJson('/api/v1/transactional/non-existent-hash');

        $response->assertStatus(404);
    }
}
