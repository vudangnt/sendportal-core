<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionalSourceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'transactional_hash' => $this->hash,
            'created_at' => $this->created_at?->toIso8601String(),
            'messages' => $this->whenLoaded('messages', function () {
                return $this->messages->map(function ($message) {
                    return [
                        'message_hash' => $message->hash,
                        'recipient' => $message->recipient_email,
                        'subject' => $message->subject,
                        'queued_at' => $message->queued_at?->toIso8601String(),
                        'sent_at' => $message->sent_at?->toIso8601String(),
                        'delivered_at' => $message->delivered_at?->toIso8601String(),
                        'opened_at' => $message->opened_at?->toIso8601String(),
                        'clicked_at' => $message->clicked_at?->toIso8601String(),
                        'bounced_at' => $message->bounced_at?->toIso8601String(),
                        'complained_at' => $message->complained_at?->toIso8601String(),
                        'open_count' => $message->open_count,
                        'click_count' => $message->click_count,
                    ];
                });
            }),
        ];
    }
}
