<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Locations;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\LocationStoreRequest;
use Sendportal\Base\Http\Requests\LocationUpdateRequest;
use Sendportal\Base\Models\Location;
use Sendportal\Base\Repositories\LocationTenantRepository;
use Illuminate\Validation\Rule;

class LocationsController extends Controller
{
    /** @var LocationTenantRepository */
    private $locationRepository;

    public function __construct(LocationTenantRepository $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * @throws Exception
     */
    public function index(): View
    {
        $locations = $this->locationRepository->all(Sendportal::currentWorkspaceId(), 'name')->toArray();
        foreach ($locations as $key => $location) {
            if ($location['parent_id'] === 0) {
                foreach ($locations as $child) {
                    if ($child['parent_id'] === $location['id']) {
                        $locations[$key]['children'][] = $child;
                    }
                }
            }
        }
        // Hàm lọc
        $locations = array_filter($locations, function ($item) {
            return $item['parent_id'] === 0;
        });

        return view('sendportal::locations.index', compact('locations'));
    }

    public function create(): View
    {
        $parentlocations = Location::where('parent_id', 0)->get();
        $types = [
            'city' => 'Thành phố',
            'state' => 'Tỉnh',
            'country' => 'Quốc gia',
        ];
        return view('sendportal::locations.create', compact('parentlocations', 'types'));
    }

    /**
     * @throws Exception
     */
    public function store(LocationStoreRequest $request): RedirectResponse
    {
        $data = $request->all();
        $name = Arr::get($request, 'name');
        $slug = Str::slug($name);
        $data['code'] = $slug;
        $this->locationRepository->store(Sendportal::currentWorkspaceId(), $data);
        return redirect()->route('sendportal.locations.index');
    }

    /**
     * @throws Exception
     */
    public function edit(int $id): View
    {
        $location = $this->locationRepository->find(Sendportal::currentWorkspaceId(), $id, ['subscribers']);
        $parentlocations = Location::where('parent_id', 0)->get();

        $types = [
            'city' => 'Thành phố',
            'state' => 'Tỉnh',
            'country' => 'Quốc gia',
        ];

        return view('sendportal::locations.edit', compact('location', 'parentlocations', 'types'));
    }

    /**
     * @throws Exception
     */
    public function update(int $id, LocationUpdateRequest $request): RedirectResponse
    {
        $this->locationRepository->update(Sendportal::currentWorkspaceId(), $id, $request->all());

        return redirect()->route('sendportal.locations.index');
    }

    /**
     * @throws Exception
     */
    public function destroy(int $id): RedirectResponse
    {
        $allChild = $this->locationRepository->getBy(
            Sendportal::currentWorkspaceId(),
            ['parent_id' => $id]
        );

        foreach ($allChild as $item) {
            $item->parent_id = 0;
            $item->save();
        }

        $this->locationRepository->destroy(Sendportal::currentWorkspaceId(), $id);

        return redirect()->route('sendportal.locations.index');
    }
}
