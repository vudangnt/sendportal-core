<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Transactional;

use Exception;
use Illuminate\Support\Facades\Log;
use Sendportal\Base\Events\MessageFailedEvent;
use Sendportal\Base\Models\EmailService;
use Sendportal\Base\Models\Message;
use Sendportal\Base\Models\TransactionalSource;
use Sendportal\Base\Services\Messages\MarkAsSent;
use Sendportal\Base\Services\Messages\MessageOptions;
use Sendportal\Base\Services\Messages\MessageTrackingOptions;
use Sendportal\Base\Services\Messages\RelayMessage;

class DispatchTransactionalMessage
{
    protected RelayMessage $relayMessage;
    protected MarkAsSent $markAsSent;

    public function __construct(RelayMessage $relayMessage, MarkAsSent $markAsSent)
    {
        $this->relayMessage = $relayMessage;
        $this->markAsSent = $markAsSent;
    }

    /**
     * Dispatch a transactional message via the resolved email service.
     */
    public function handle(Message $message, EmailService $emailService): ?string
    {
        if ($message->sent_at) {
            return null;
        }

        try {
            $source = $message->source;

            $payload = $source instanceof TransactionalSource ? ($source->request_payload ?? []) : [];
            $content = $payload['content'] ?? [];
            $tracking = $payload['tracking'] ?? [];

            $body = $content['type'] === 'mime'
                ? ($content['mime'] ?? '')
                : ($content['html'] ?? '');

            $trackingOptions = (new MessageTrackingOptions())
                ->setIsOpenTracking((bool) ($tracking['open'] ?? true))
                ->setIsClickTracking((bool) ($tracking['click'] ?? true));

            $options = (new MessageOptions())
                ->setTo($message->recipient_email)
                ->setFromEmail($message->from_email)
                ->setFromName((string) $message->from_name)
                ->setSubject($message->subject)
                ->setTrackingOptions($trackingOptions);

            $messageId = $this->relayMessage->handle($body, $options, $emailService);

            if (!$messageId) {
                throw new Exception('Email provider did not return a message id');
            }

            $this->markAsSent->handle($message, $messageId);

            Log::info('Transactional message dispatched.', [
                'message_id' => $messageId,
                'sendportal_message_id' => $message->id,
            ]);

            return $messageId;
        } catch (Exception $e) {
            Log::error('Transactional message dispatch failed.', [
                'sendportal_message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);

            event(new MessageFailedEvent($message, $e->getMessage()));

            throw $e;
        }
    }
}
