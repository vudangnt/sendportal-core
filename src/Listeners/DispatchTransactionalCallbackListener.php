<?php

declare(strict_types=1);

namespace Sendportal\Base\Listeners;

use Sendportal\Base\Events\MessageBouncedEvent;
use Sendportal\Base\Events\MessageClickedEvent;
use Sendportal\Base\Events\MessageComplainedEvent;
use Sendportal\Base\Events\MessageDeliveredEvent;
use Sendportal\Base\Events\MessageFailedEvent;
use Sendportal\Base\Events\MessageOpenedEvent;
use Sendportal\Base\Events\MessageSentEvent;
use Sendportal\Base\Jobs\DispatchTransactionalCallbackJob;
use Sendportal\Base\Models\Message;
use Sendportal\Base\Models\TransactionalSource;

class DispatchTransactionalCallbackListener
{
    public function handleSent(MessageSentEvent $event): void
    {
        $this->dispatch($event->message, 'sent');
    }

    public function handleDelivered(MessageDeliveredEvent $event): void
    {
        $this->dispatch($event->message, 'delivered');
    }

    public function handleOpened(MessageOpenedEvent $event): void
    {
        $this->dispatch($event->message, 'opened', [
            'open_count' => $event->message->open_count,
        ]);
    }

    public function handleClicked(MessageClickedEvent $event): void
    {
        $this->dispatch($event->message, 'clicked', [
            'click_count' => $event->message->click_count,
            'click_url' => $event->url,
        ]);
    }

    public function handleBounced(MessageBouncedEvent $event): void
    {
        $this->dispatch($event->message, 'bounced');
    }

    public function handleComplained(MessageComplainedEvent $event): void
    {
        $this->dispatch($event->message, 'complained');
    }

    public function handleFailed(MessageFailedEvent $event): void
    {
        $this->dispatch($event->message, 'failed', [
            'failure_reason' => $event->reason,
        ]);
    }

    protected function dispatch(Message $message, string $event, array $data = []): void
    {
        if ($message->source_type !== TransactionalSource::class) {
            return;
        }

        DispatchTransactionalCallbackJob::dispatch($message->id, $event, $data);
    }
}
