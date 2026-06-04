<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Transactional;

use Illuminate\Support\Str;
use Sendportal\Base\Models\EmailService;

class TransactionalEmailServiceResolver
{
    public function resolve(int $workspaceId, string $fromEmail): ?EmailService
    {
        $service = $this->matchByDomain($workspaceId, $fromEmail);

        if ($service) {
            return $service;
        }

        return EmailService::where('workspace_id', $workspaceId)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Strict resolution for transactional sends: the from-email domain MUST be
     * whitelisted in an email service's sender_domains. No is_default fallback
     * — returns null if no configured service covers the domain.
     */
    public function resolveStrict(int $workspaceId, string $fromEmail): ?EmailService
    {
        return $this->matchByDomain($workspaceId, $fromEmail);
    }

    private function matchByDomain(int $workspaceId, string $fromEmail): ?EmailService
    {
        $domain = strtolower(Str::after($fromEmail, '@'));

        return EmailService::where('workspace_id', $workspaceId)
            ->whereNotNull('sender_domains')
            ->get()
            ->first(function (EmailService $service) use ($domain) {
                $domains = array_map('strtolower', $service->sender_domains ?? []);
                return in_array($domain, $domains, true);
            });
    }
}
