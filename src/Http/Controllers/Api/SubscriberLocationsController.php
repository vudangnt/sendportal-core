<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\Api\SubscriberLocationDestroyRequest;
use Sendportal\Base\Http\Requests\Api\SubscriberLocationStoreRequest;
use Sendportal\Base\Http\Requests\Api\SubscriberLocationUpdateRequest;
use Sendportal\Base\Http\Resources\Location as LocationResource;
use Sendportal\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;
use Sendportal\Base\Services\Subscribers\Locations\ApiSubscriberLocationservice;

class SubscriberLocationsController extends Controller
{
    /** @var SubscriberTenantRepositoryInterface */
    private $subscribers;

    /** @var ApiSubscriberlocationservice */
    private $apiService;

    public function __construct(
        SubscriberTenantRepositoryInterface $subscribers,
        ApiSubscriberLocationservice $apiService
    ) {
        $this->subscribers = $subscribers;
        $this->apiService = $apiService;
    }

    /**
     * @throws Exception
     */
    public function index(int $subscriberId): AnonymousResourceCollection
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $subscriber = $this->subscribers->find($workspaceId, $subscriberId, ['locations']);

        return LocationResource::collection($subscriber->locations);
    }

    /**
     * @throws Exception
     */
    public function store(SubscriberLocationStoreRequest $request, int $subscriberId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = Sendportal::currentWorkspaceId();
        $locations = $this->apiService->store($workspaceId, $subscriberId, collect($input['locations']));

        return LocationResource::collection($locations);
    }

    /**
     * @throws Exception
     */
    public function update(SubscriberLocationUpdateRequest $request, int $subscriberId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = Sendportal::currentWorkspaceId();
        $locations = $this->apiService->update($workspaceId, $subscriberId, collect($input['locations']));

        return LocationResource::collection($locations);
    }

    /**
     * @throws Exception
     */
    public function destroy(SubscriberLocationDestroyRequest $request, int $subscriberId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = Sendportal::currentWorkspaceId();
        $locations = $this->apiService->destroy($workspaceId, $subscriberId, collect($input['locations']));

        return LocationResource::collection($locations);
    }
}
