<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\locations;

use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Sendportal\Base\Repositories\locationTenantRepository;

class ApiLocationSubscriberService
{
    /** @var locationTenantRepository */
    private $locations;

    public function __construct(locationTenantRepository $locations)
    {
        $this->locations = $locations;
    }

    /**
     * Add new subscribers to a location.
     *
     * @throws Exception
     */
    public function store(int $workspaceId, int $locationId, Collection $subscriberIds): Collection
    {
        $location = $this->locations->find($workspaceId, $locationId);

        /** @var Collection $existingSubscribers */
        $existingSubscribers = $location->subscribers()->pluck('sendportal_subscribers.id')->toBase();

        $subscribersToStore = $subscriberIds->diff($existingSubscribers);

        $location->subscribers()->attach($subscribersToStore);

        return $location->subscribers->toBase();
    }

    /**
     * Sync subscribers on a location.
     *
     * @throws Exception
     */
    public function update(int $workspaceId, int $locationId, Collection $subscriberIds): EloquentCollection
    {
        $location = $this->locations->find($workspaceId, $locationId);

        $location->subscribers()->sync($subscriberIds);

        $location->load('subscribers');

        return $location->subscribers;
    }

    /**
     * Remove subscribers from a location.
     *
     * @throws Exception
     */
    public function destroy(int $workspaceId, int $locationId, Collection $subscriberIds): EloquentCollection
    {
        $location = $this->locations->find($workspaceId, $locationId);

        $location->subscribers()->detach($subscriberIds);

        return $location->subscribers;
    }
}
