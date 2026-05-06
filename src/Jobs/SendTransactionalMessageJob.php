<?php

declare(strict_types=1);

namespace Sendportal\Base\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sendportal\Base\Models\EmailService;
use Sendportal\Base\Models\Message;
use Sendportal\Base\Services\Transactional\DispatchTransactionalMessage;

class SendTransactionalMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected int $messageId;
    protected int $emailServiceId;

    public function __construct(int $messageId, int $emailServiceId)
    {
        $this->messageId = $messageId;
        $this->emailServiceId = $emailServiceId;
    }

    public function handle(DispatchTransactionalMessage $dispatch): void
    {
        /** @var Message|null $message */
        $message = Message::find($this->messageId);

        /** @var EmailService|null $emailService */
        $emailService = EmailService::find($this->emailServiceId);

        if (!$message || !$emailService) {
            return;
        }

        $dispatch->handle($message, $emailService);
    }
}
