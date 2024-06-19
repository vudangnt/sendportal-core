<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Locations;

use Exception;
use Illuminate\Support\Collection;
use Sendportal\Base\Models\Location;
use Sendportal\Base\Repositories\LocationTenantRepository;

class ApiLocationService
{
    /** @var LocationTenantRepository */
    private $locations;

    public function __construct(LocationTenantRepository $locations)
    {
        $this->locations = $locations;
    }

    /**
     * Store a new tag, optionally including attached subscribers.
     *
     * @throws Exception
     */
    public function store(int $workspaceId, Collection $data): Location
    {
        $location = $this->locations->store($workspaceId, $data->except('subscribers')->toArray());

        if (!empty($data['subscribers'])) {
            $location->subscribers()->attach($data['subscribers']);
        }

        return $location;
    }
}
