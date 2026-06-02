<?php

namespace Sendportal\Base\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Sendportal\Base\Models\Template;

class TemplateTenantRepository extends BaseTenantRepository
{
    protected $modelName = Template::class;

    public function getQueryBuilder($workspaceId): Builder
    {
        return parent::getQueryBuilder($workspaceId)
            ->where('kind', Template::KIND_CAMPAIGN);
    }

    protected function applyFilters(Builder $instance, array $filters = []): void
    {
        parent::applyFilters($instance, $filters);
        if (isset($filters['status'])) {
            $instance->where('status', $filters['status']);
        }
    }
}
