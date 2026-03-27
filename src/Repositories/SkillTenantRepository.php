<?php

namespace Sendportal\Base\Repositories;

use Sendportal\Base\Models\Skill;

class SkillTenantRepository extends BaseTenantRepository
{
    protected $modelName = Skill::class;

    public function update($workspaceId, $id, array $data)
    {
        $instance = $this->find($workspaceId, $id);
        $this->executeSave($workspaceId, $instance, $data);
        return $instance;
    }

    public function syncSubscribers(Skill $skill, array $subscribers = [])
    {
        return $skill->subscribers()->sync($subscribers);
    }

    public function destroy($workspaceId, $id): bool
    {
        $instance = $this->find($workspaceId, $id);
        $instance->subscribers()->detach();
        return $instance->delete();
    }
}
