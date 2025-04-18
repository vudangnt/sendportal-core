<?php

namespace Sendportal\Base\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Sendportal\Base\Models\Template;

class TemplateTenantRepository extends BaseTenantRepository
{
    protected $modelName = Template::class;

    protected function applyFilters(Builder $instance, array $filters = []): void
    {
        parent::applyFilters($instance, $filters); // TODO: Change the autogenerated stub
        if (isset($filters['status'])) {
            $instance->where('status', $filters['status']);
        }
    }
}
