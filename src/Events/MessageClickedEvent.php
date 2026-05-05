<?php

declare(strict_types=1);

namespace Sendportal\Base\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Sendportal\Base\Models\Message;

class MessageClickedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public ?string $url;

    public function __construct(Message $message, ?string $url = null)
    {
        $this->message = $message;
        $this->url = $url;
    }
}
