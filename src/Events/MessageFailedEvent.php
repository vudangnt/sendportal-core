<?php

declare(strict_types=1);

namespace Sendportal\Base\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Sendportal\Base\Models\Message;

class MessageFailedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public string $reason;

    public function __construct(Message $message, string $reason = '')
    {
        $this->message = $message;
        $this->reason = $reason;
    }
}
