<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\Api\LocationSubscriberDestroyRequest;
use Sendportal\Base\Http\Requests\Api\LocationSubscriberStoreRequest;
use Sendportal\Base\Http\Requests\Api\LocationSubscriberUpdateRequest;
use Sendportal\Base\Http\Resources\Subscriber as SubscriberResource;
use Sendportal\Base\Repositories\LocationTenantRepository;
use Sendportal\Base\Services\Locations\ApiLocationSubscriberService;

class LocationSubscribersController extends Controller
{
    /** @var LocationTenantRepository */
    private $locations;

    /** @var ApiLocationSubscriberService */
    private $apiService;

    public function __construct(
        LocationTenantRepository $locations,
        ApiLocationSubscriberService $apiService
    ) {
        $this->locations = $locations;
        $this->apiService = $apiService;
    }

    /**
     * @throws Exception
     */
    public function index(int $locationId): AnonymousResourceCollection
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $location = $this->locations->find($workspaceId, $locationId, ['subscribers']);

        return SubscriberResource::collection($location->subscribers);
    }

    /**
     * @throws Exception
     */
    public function store(LocationSubscriberUpdateRequest $request, int $locationId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = Sendportal::currentWorkspaceId();
        $subscribers = $this->apiService->store($workspaceId, $locationId, collect($input['subscribers']));

        return SubscriberResource::collection($subscribers);
    }

    /**
     * @throws Exception
     */
    public function update(LocationSubscriberUpdateRequest $request, int $locationId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = Sendportal::currentWorkspaceId();
        $subscribers = $this->apiService->update($workspaceId, $locationId, collect($input['subscribers']));

        return SubscriberResource::collection($subscribers);
    }

    /**
     * @throws Exception
     */
    public function destroy(LocationSubscriberDestroyRequest $request, int $locationId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = Sendportal::currentWorkspaceId();
        $subscribers = $this->apiService->destroy($workspaceId, $locationId, collect($input['subscribers']));

        return SubscriberResource::collection($subscribers);
    }
}
