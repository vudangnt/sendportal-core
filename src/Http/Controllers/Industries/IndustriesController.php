<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Industries;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\IndustryStoreRequest;
use Sendportal\Base\Http\Requests\IndustryUpdateRequest;
use Sendportal\Base\Repositories\IndustryTenantRepository;

class IndustriesController extends Controller
{
    /** @var IndustryTenantRepository */
    private $industryRepository;

    public function __construct(IndustryTenantRepository $industryRepository)
    {
        $this->industryRepository = $industryRepository;
    }

    /**
     * @throws Exception
     */
    public function index(): View
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $industries = $this->industryRepository->all($workspaceId, 'name')->toArray();

        foreach ($industries as $key => $industry) {
            if ($industry['parent_id'] === 0) {
                foreach ($industries as $child) {
                    if ($child['parent_id'] === $industry['id']) {
                        $industries[$key]['children'][] = $child;
                    }
                }

                $industryModel = $this->industryRepository->find($workspaceId, $industry['id']);
                if ($industryModel) {
                    $industries[$key]['active_subscribers_count'] = $industryModel->total_active_subscribers_count;
                }
            }
        }

        $industries = array_filter($industries, function ($item) {
            return $item['parent_id'] === 0;
        });

        return view('sendportal::industries.index', compact('industries'));
    }

    public function create(): View
    {
        $parentIndustries = $this->industryRepository->getQueryBuilder(Sendportal::currentWorkspaceId())
            ->where('parent_id', 0)->get();
        return view('sendportal::industries.create', compact('parentIndustries'));
    }

    /**
     * @throws Exception
     */
    public function store(IndustryStoreRequest $request): RedirectResponse
    {
        $this->industryRepository->store(Sendportal::currentWorkspaceId(), $request->all());
        return redirect()->route('sendportal.industries.index');
    }

    /**
     * @throws Exception
     */
    public function edit(int $id): View
    {
        $industry = $this->industryRepository->find(Sendportal::currentWorkspaceId(), $id, ['subscribers']);
        $parentIndustries = $this->industryRepository->getQueryBuilder(Sendportal::currentWorkspaceId())
            ->where('parent_id', 0)->get();
        return view('sendportal::industries.edit', compact('industry', 'parentIndustries'));
    }

    /**
     * @throws Exception
     */
    public function update(int $id, IndustryUpdateRequest $request): RedirectResponse
    {
        $this->industryRepository->update(Sendportal::currentWorkspaceId(), $id, $request->all());
        return redirect()->route('sendportal.industries.index');
    }

    /**
     * @throws Exception
     */
    public function destroy(int $id): RedirectResponse
    {
        $allChild = $this->industryRepository->getBy(
            Sendportal::currentWorkspaceId(),
            ['parent_id' => $id]
        );

        foreach ($allChild as $item) {
            $item->parent_id = 0;
            $item->save();
        }

        $this->industryRepository->destroy(Sendportal::currentWorkspaceId(), $id);
        return redirect()->route('sendportal.industries.index');
    }
}
