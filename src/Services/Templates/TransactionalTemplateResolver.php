<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Templates;

use Sendportal\Base\Models\Template;

class TransactionalTemplateResolver
{
    /**
     * Resolve a transactional template for the given workspace + code.
     * Order: workspace override → global default → 422.
     */
    public function resolveTemplate(int $workspaceId, string $code): Template
    {
        $workspaceCopy = Template::transactional()
            ->where('workspace_id', $workspaceId)
            ->where('code', $code)
            ->first();

        if ($workspaceCopy) {
            return $workspaceCopy;
        }

        $default = Template::transactional()
            ->whereNull('workspace_id')
            ->where('is_default', true)
            ->where('code', $code)
            ->first();

        if ($default) {
            return $default;
        }

        abort(422, "Template not found for code: {$code}");
    }
}
