<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Subscribers\Locations;

use Exception;
use Illuminate\Support\Collection;
use Sendportal\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;

class ApiSubscriberLocationService
{
    /** @var SubscriberTenantRepositoryInterface */
    private $subscribers;

    public function __construct(SubscriberTenantRepositoryInterface $subscribers)
    {
        $this->subscribers = $subscribers;
    }

    /**
     * Add locations to a subscriber.
     *
     * @param int $workspaceId
     * @param int $subscriberId
     * @param Collection $locationIds
     *
     * @return Collection
     * @throws Exception
     */
    public function store(int $workspaceId, int $subscriberId, Collection $locationIds): Collection
    {
        $subscriber = $this->subscribers->find($workspaceId, $subscriberId);

        /** @var Collection $existinglocations */
        $existinglocations = $subscriber->locations()->pluck('tag.id')->toBase();

        $locationsToStore = $locationIds->diff($existinglocations);

        $subscriber->locations()->attach($locationsToStore);

        return $subscriber->locations->toBase();
    }

    /**
     * Sync the list of locations a subscriber is associated with.
     *
     * @param int $workspaceId
     * @param int $subscriberId
     * @param Collection $locationIds
     *
     * @return Collection
     * @throws Exception
     */
    public function update(int $workspaceId, int $subscriberId, Collection $locationIds): Collection
    {
        $subscriber = $this->subscribers->find($workspaceId, $subscriberId, ['locations']);

        $subscriber->locations()->sync($locationIds);

        $subscriber->load('locations');

        return $subscriber->locations->toBase();
    }

    /**
     * Remove locations from a subscriber.
     *
     * @param int $workspaceId
     * @param int $subscriberId
     * @param Collection $locationIds
     *
     * @return Collection
     * @throws Exception
     */
    public function destroy(int $workspaceId, int $subscriberId, Collection $locationIds): Collection
    {
        $subscriber = $this->subscribers->find($workspaceId, $subscriberId);

        $subscriber->locations()->detach($locationIds);

        return $subscriber->locations;
    }
}
