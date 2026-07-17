<?php

namespace Sendportal\Base\Interfaces;

use Sendportal\Base\Services\Messages\MessageTrackingOptions;

interface MailAdapterInterface
{
    /**
     * Send an email.
     *
     * @param string $fromEmail
     * @param string $fromName
     * @param string $toEmail
     * @param string $subject
     * @param MessageTrackingOptions $trackingOptions
     * @param string $content
     * @param array<int, array{filename: string, content_type: string, body: string}> $attachments
     *        Optional file attachments. Adapters that cannot deliver them MUST throw
     *        rather than silently dropping the files (see BaseMailAdapter::guardAttachments).
     *
     * @return string
     */
    public function send(string $fromEmail, string $fromName, string $toEmail, string $subject, MessageTrackingOptions $trackingOptions, string $content, array $attachments = []): string;
}
