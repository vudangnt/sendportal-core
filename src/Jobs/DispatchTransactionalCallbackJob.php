<?php

declare(strict_types=1);

namespace Sendportal\Base\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Sendportal\Base\Models\Message;
use Sendportal\Base\Models\TransactionalSource;
use Sendportal\Base\Services\Transactional\HmacSigner;

class DispatchTransactionalCallbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 60, 300];

    protected int $messageId;
    protected string $event;
    protected array $data;

    public function __construct(int $messageId, string $event, array $data = [])
    {
        $this->messageId = $messageId;
        $this->event = $event;
        $this->data = $data;
    }

    public function handle(HmacSigner $signer): void
    {
        /** @var Message|null $message */
        $message = Message::find($this->messageId);

        if (!$message || $message->source_type !== TransactionalSource::class) {
            return;
        }

        $workspace = DB::table('workspaces')->where('id', $message->workspace_id)->first();

        if (!$workspace || empty($workspace->transactional_callback_url)) {
            return;
        }

        /** @var TransactionalSource|null $source */
        $source = TransactionalSource::find($message->source_id);

        if (!$source) {
            return;
        }

        $timestamp = time();

        $payload = [
            'event' => $this->event,
            'transactional_hash' => $source->hash,
            'message_hash' => $message->hash,
            'recipient' => $message->recipient_email,
            'timestamp' => date('c', $timestamp),
            'data' => $this->data,
            'metadata' => $source->request_payload['metadata'] ?? [],
        ];

        $body = json_encode($payload);
        $signature = $signer->sign($body, (string) $workspace->transactional_callback_secret, $timestamp);

        Http::timeout(10)
            ->withHeaders([
                'X-Sendportal-Event' => $this->event,
                'X-Sendportal-Signature' => $signature,
                'X-Sendportal-Timestamp' => (string) $timestamp,
            ])
            ->post($workspace->transactional_callback_url, $payload);
    }
}
