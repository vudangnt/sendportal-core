<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\Api\LocationStoreRequest;
use Sendportal\Base\Http\Requests\Api\TagUpdateRequest;
use Sendportal\Base\Http\Resources\Location as LocationResource;
use Sendportal\Base\Repositories\LocationTenantRepository;
use Sendportal\Base\Services\Locations\ApiLocationService;

class LocationsController extends Controller
{
    /** @var LocationTenantRepository */
    private $locations;

    /** @var ApiLocationService */
    private $apiService;

    public function __construct(
        LocationTenantRepository $locations,
        ApiLocationService $apiService
    ) {
        $this->locations = $locations;
        $this->apiService = $apiService;
    }

    /**
     * @throws Exception
     */
    public function index(): AnonymousResourceCollection
    {
        $workspaceId = Sendportal::currentWorkspaceId();

        return LocationResource::collection(
            $this->locations->paginate($workspaceId, 'name', [], request()->get('per_page', 25))
        );
    }

    /**
     * @throws Exception
     */
    public function store(locationstoreRequest $request): LocationResource
    {
        $input = $request->validated();
        $workspaceId = Sendportal::currentWorkspaceId();
        $location = $this->apiService->store($workspaceId, collect($input));

        $location->load('subscribers');

        return new LocationResource($location);
    }

    /**
     * @throws Exception
     */
    public function show(int $id): LocationResource
    {
        $workspaceId = Sendportal::currentWorkspaceId();

        return new LocationResource($this->locations->find($workspaceId, $id));
    }

    /**
     * @throws Exception
     */
    public function update(TagUpdateRequest $request, int $id): LocationResource
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $location = $this->locations->update($workspaceId, $id, $request->validated());

        return new LocationResource($location);
    }

    /**
     * @throws Exception
     */
    public function destroy(int $id): Response
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $this->locations->destroy($workspaceId, $id);

        return response(null, 204);
    }
}
