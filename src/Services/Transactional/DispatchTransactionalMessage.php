<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Transactional;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
    protected AttachmentFetcher $attachmentFetcher;

    public function __construct(RelayMessage $relayMessage, MarkAsSent $markAsSent, AttachmentFetcher $attachmentFetcher)
    {
        $this->relayMessage = $relayMessage;
        $this->markAsSent = $markAsSent;
        $this->attachmentFetcher = $attachmentFetcher;
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

            $body = ($content['type'] ?? 'html') === 'mime'
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
                ->setTrackingOptions($trackingOptions)
                ->setAttachments($this->loadAttachments($payload));

            $messageId = $this->relayMessage->handle($body, $options, $emailService);

            if (!$messageId) {
                throw new Exception('Email provider did not return a message id');
            }

            $this->markAsSent->handle($message, $messageId);

            $this->cleanupAttachments($message);

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

    /**
     * Read the attachment bytes stored at request time back off the disk.
     *
     * @return array<int, array{filename: string, content_type: string, body: string}>
     */
    protected function loadAttachments(array $payload): array
    {
        $attachments = [];

        foreach ($payload['attachments'] ?? [] as $attachment) {
            $path = $attachment['path'] ?? null;

            if (!$path || !Storage::exists($path)) {
                throw new Exception(sprintf(
                    'Attachment "%s" is no longer available in storage (%s).',
                    $attachment['filename'] ?? 'unknown',
                    (string) $path
                ));
            }

            $attachments[] = [
                'filename' => $attachment['filename'] ?? 'attachment',
                'content_type' => $attachment['content_type'] ?? 'application/octet-stream',
                'body' => (string) Storage::get($path),
            ];
        }

        return $attachments;
    }

    /**
     * Drop the temporary attachment bytes once every recipient of this source
     * has been sent (messages of one source share the same stored files).
     */
    protected function cleanupAttachments(Message $message): void
    {
        $source = $message->source;

        if (!$source instanceof TransactionalSource) {
            return;
        }

        $paths = array_filter(array_column($source->request_payload['attachments'] ?? [], 'path'));

        if ($paths === []) {
            return;
        }

        $pending = Message::where('source_type', TransactionalSource::class)
            ->where('source_id', $source->id)
            ->whereNull('sent_at')
            ->exists();

        if (!$pending) {
            $this->attachmentFetcher->cleanup($paths);
        }
    }
}
