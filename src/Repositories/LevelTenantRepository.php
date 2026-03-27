<?php

namespace Sendportal\Base\Repositories;

use Sendportal\Base\Models\Level;

class LevelTenantRepository extends BaseTenantRepository
{
    protected $modelName = Level::class;

    public function update($workspaceId, $id, array $data)
    {
        $instance = $this->find($workspaceId, $id);
        $this->executeSave($workspaceId, $instance, $data);
        return $instance;
    }

    public function syncSubscribers(Level $level, array $subscribers = [])
    {
        return $level->subscribers()->sync($subscribers);
    }

    public function destroy($workspaceId, $id): bool
    {
        $instance = $this->find($workspaceId, $id);
        $instance->subscribers()->detach();
        return $instance->delete();
    }
}
