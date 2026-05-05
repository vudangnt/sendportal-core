<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Transactional;

use Illuminate\Support\Str;
use Sendportal\Base\Models\EmailService;

class TransactionalEmailServiceResolver
{
    public function resolve(int $workspaceId, string $fromEmail): ?EmailService
    {
        $domain = strtolower(Str::after($fromEmail, '@'));

        $service = EmailService::where('workspace_id', $workspaceId)
            ->whereNotNull('sender_domains')
            ->get()
            ->first(function (EmailService $service) use ($domain) {
                $domains = array_map('strtolower', $service->sender_domains ?? []);
                return in_array($domain, $domains, true);
            });

        if ($service) {
            return $service;
        }

        return EmailService::where('workspace_id', $workspaceId)
            ->where('is_default', true)
            ->first();
    }
}
