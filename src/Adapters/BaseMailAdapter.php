<?php

namespace Sendportal\Base\Adapters;

use RuntimeException;
use Sendportal\Base\Interfaces\MailAdapterInterface;

abstract class BaseMailAdapter implements MailAdapterInterface
{
    /** @var array */
    protected $config;

    /**
     * Fail loudly when attachments are requested from a provider that does not
     * implement them yet — silently dropping files would ship an email that is
     * missing what the caller asked for.
     */
    protected function guardAttachments(array $attachments): void
    {
        if ($attachments !== []) {
            throw new RuntimeException(sprintf(
                'Attachments are not supported by the %s email service yet. Use an SES email service to send attachments.',
                class_basename(static::class)
            ));
        }
    }

    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }
}
