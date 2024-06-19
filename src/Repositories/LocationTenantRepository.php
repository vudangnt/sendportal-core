<?php

namespace Sendportal\Base\Repositories;

use Sendportal\Base\Models\Location;

class LocationTenantRepository extends BaseTenantRepository
{
    /**
     * @var string
     */
    protected $modelName = Location::class;

    /**
     * {@inheritDoc}
     */
    public function update($workspaceId, $id, array $data)
    {
        $instance = $this->find($workspaceId, $id);

        $this->executeSave($workspaceId, $instance, $data);

        return $instance;
    }

    /**
     * Sync subscribers
     *
     * @param Tag $tag
     * @param array $subscribers
     * @return array
     */
    public function syncSubscribers(Location $location, array $subscribers = [])
    {
        return $location->subscribers()->sync($subscribers);
    }

    /**
     * {@inheritDoc}
     */
    public function destroy($workspaceId, $id): bool
    {
        $instance = $this->find($workspaceId, $id);

        $instance->subscribers()->detach();

        return $instance->delete();
    }
}
