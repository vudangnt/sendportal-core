<?php

declare(strict_types=1);

namespace Sendportal\Base\Adapters;

use Aws\Result;
use Aws\Ses\SesClient;
use Aws\SesV2\SesV2Client;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Sendportal\Base\Services\Messages\MessageTrackingOptions;
use Sendportal\Base\Traits\ThrottlesSending;
use Symfony\Component\Mime\Email;

class SesMailAdapter extends BaseMailAdapter
{
    use ThrottlesSending;

    /** @var SesClient */
    protected $client;

    /** @var SesV2Client|null */
    protected $clientV2;

    /**
     * @throws BindingResolutionException
     */
    public function send(string $fromEmail, string $fromName, string $toEmail, string $subject, MessageTrackingOptions $trackingOptions, string $content, array $attachments = []): string
    {
        // TODO(david): It isn't clear whether it is possible to set per-message tracking for SES.

        // Attachments require a raw MIME message. The v1 SendRawEmail API caps the
        // encoded message at 10MB, so attachment sends go through SES v2 (40MB).
        // Without attachments we keep the simple v1 path unchanged.
        if ($attachments !== []) {
            return $this->sendRawV2($fromEmail, $fromName, $toEmail, $subject, $content, $attachments);
        }

        $result = $this->throttleSending(function () use ($fromEmail, $fromName, $toEmail, $subject, $trackingOptions, $content) {
            return $this->resolveClient()->sendEmail([
                'Source' => $fromName . ' <' . $fromEmail . '>',

                'Destination' => [
                    'ToAddresses' => [$toEmail],
                ],

                'Message' => [
                    'Subject' => [
                        'Data' => $subject,
                    ],
                    'Body' => [
                        'Html' => [
                            'Data' => $content,
                        ],
                    ],
                ],
                'ConfigurationSetName' => Arr::get($this->config, 'configuration_set_name'),
            ]);
        });

        return $this->resolveMessageId($result);
    }

    /**
     * Send a message with attachments as raw MIME via the SES v2 API.
     *
     * SES applies the configuration set's open/click tracking to raw messages too
     * (an HTML part is present), so tracking still flows to the SNS webhook.
     *
     * @param array<int, array{filename: string, content_type: string, body: string}> $attachments
     * @throws BindingResolutionException
     */
    protected function sendRawV2(string $fromEmail, string $fromName, string $toEmail, string $subject, string $content, array $attachments): string
    {
        $email = (new Email())
            ->from(trim($fromName) !== '' ? sprintf('%s <%s>', $fromName, $fromEmail) : $fromEmail)
            ->to($toEmail)
            ->subject($subject)
            ->html($content);

        foreach ($attachments as $attachment) {
            $email->attach(
                $attachment['body'],
                $attachment['filename'] ?? 'attachment',
                $attachment['content_type'] ?? 'application/octet-stream'
            );
        }

        $raw = $email->toString();

        $result = $this->throttleSending(function () use ($raw) {
            return $this->resolveClientV2()->sendEmail(array_filter([
                'Content' => ['Raw' => ['Data' => $raw]],
                'ConfigurationSetName' => Arr::get($this->config, 'configuration_set_name'),
            ]));
        });

        return Arr::get($result->toArray(), 'MessageId');
    }

    /**
     * @throws BindingResolutionException
     */
    protected function resolveClientV2(): SesV2Client
    {
        if ($this->clientV2) {
            return $this->clientV2;
        }

        $this->clientV2 = app()->make('aws')->createClient('sesv2', [
            'region' => Arr::get($this->config, 'region'),
            'credentials' => [
                'key' => Arr::get($this->config, 'key'),
                'secret' => Arr::get($this->config, 'secret'),
            ]
        ]);

        return $this->clientV2;
    }

    /**
     * @throws BindingResolutionException
     */
    protected function resolveClient(): SesClient
    {
        if ($this->client) {
            return $this->client;
        }

        $this->client = app()->make('aws')->createClient('ses', [
            'region' => Arr::get($this->config, 'region'),
            'credentials' => [
                'key' => Arr::get($this->config, 'key'),
                'secret' => Arr::get($this->config, 'secret'),
            ]
        ]);

        return $this->client;
    }

    protected function resolveMessageId(Result $result): string
    {
        return Arr::get($result->toArray(), 'MessageId');
    }

    /**
     * https://docs.aws.amazon.com/ses/latest/APIReference/API_GetSendQuota.html
     *
     * @throws BindingResolutionException
     */
    public function getSendQuota(): array
    {
        return $this->resolveClient()->getSendQuota()->toArray();
    }
}
