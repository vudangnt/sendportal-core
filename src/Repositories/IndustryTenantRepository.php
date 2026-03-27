<?php

namespace Sendportal\Base\Repositories;

use Sendportal\Base\Models\Industry;

class IndustryTenantRepository extends BaseTenantRepository
{
    protected $modelName = Industry::class;

    public function update($workspaceId, $id, array $data)
    {
        $instance = $this->find($workspaceId, $id);
        $this->executeSave($workspaceId, $instance, $data);
        return $instance;
    }

    public function syncSubscribers(Industry $industry, array $subscribers = [])
    {
        return $industry->subscribers()->sync($subscribers);
    }

    public function destroy($workspaceId, $id): bool
    {
        $instance = $this->find($workspaceId, $id);
        $instance->subscribers()->detach();
        return $instance->delete();
    }
}
