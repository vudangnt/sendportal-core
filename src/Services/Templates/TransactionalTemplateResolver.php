<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Templates;

use Illuminate\Support\Collection;
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

    /**
     * Build the inheritance view for a workspace, using the SAME precedence as
     * resolveTemplate(). Returns Template models decorated with `source_status`:
     *   - 'customized' : workspace override of a default code (API uses this)
     *   - 'inherited'  : default the workspace has not overridden (API uses default)
     *   - 'custom'     : workspace code with no matching default
     * Sorted by code.
     */
    public function listForWorkspace(int $workspaceId): Collection
    {
        $overrides = Template::transactional()
            ->where('workspace_id', $workspaceId)
            ->get()
            ->keyBy('code');

        $defaults = Template::transactional()
            ->whereNull('workspace_id')
            ->where('is_default', true)
            ->get()
            ->keyBy('code');

        $items = collect();

        foreach ($defaults as $code => $default) {
            if ($overrides->has($code)) {
                $t = $overrides->get($code);
                $t->setAttribute('source_status', 'customized');
                $items->push($t);
            } else {
                $default->setAttribute('source_status', 'inherited');
                $items->push($default);
            }
        }

        foreach ($overrides as $code => $override) {
            if (!$defaults->has($code)) {
                $override->setAttribute('source_status', 'custom');
                $items->push($override);
            }
        }

        return $items->sortBy('code')->values();
    }
}
