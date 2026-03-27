<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Skills;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\SkillStoreRequest;
use Sendportal\Base\Http\Requests\SkillUpdateRequest;
use Sendportal\Base\Repositories\SkillTenantRepository;

class SkillsController extends Controller
{
    /** @var SkillTenantRepository */
    private $skillRepository;

    public function __construct(SkillTenantRepository $skillRepository)
    {
        $this->skillRepository = $skillRepository;
    }

    /**
     * @throws Exception
     */
    public function index(): View
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $skills = $this->skillRepository->all($workspaceId, 'name')->toArray();

        foreach ($skills as $key => $skill) {
            if ($skill['parent_id'] === 0) {
                foreach ($skills as $child) {
                    if ($child['parent_id'] === $skill['id']) {
                        $skills[$key]['children'][] = $child;
                    }
                }

                $skillModel = $this->skillRepository->find($workspaceId, $skill['id']);
                if ($skillModel) {
                    $skills[$key]['active_subscribers_count'] = $skillModel->total_active_subscribers_count;
                }
            }
        }

        $skills = array_filter($skills, function ($item) {
            return $item['parent_id'] === 0;
        });

        return view('sendportal::skills.index', compact('skills'));
    }

    public function create(): View
    {
        $parentSkills = $this->skillRepository->getQueryBuilder(Sendportal::currentWorkspaceId())
            ->where('parent_id', 0)->get();
        return view('sendportal::skills.create', compact('parentSkills'));
    }

    /**
     * @throws Exception
     */
    public function store(SkillStoreRequest $request): RedirectResponse
    {
        $this->skillRepository->store(Sendportal::currentWorkspaceId(), $request->all());
        return redirect()->route('sendportal.skills.index');
    }

    /**
     * @throws Exception
     */
    public function edit(int $id): View
    {
        $skill = $this->skillRepository->find(Sendportal::currentWorkspaceId(), $id, ['subscribers']);
        $parentSkills = $this->skillRepository->getQueryBuilder(Sendportal::currentWorkspaceId())
            ->where('parent_id', 0)->get();
        return view('sendportal::skills.edit', compact('skill', 'parentSkills'));
    }

    /**
     * @throws Exception
     */
    public function update(int $id, SkillUpdateRequest $request): RedirectResponse
    {
        $this->skillRepository->update(Sendportal::currentWorkspaceId(), $id, $request->all());
        return redirect()->route('sendportal.skills.index');
    }

    /**
     * @throws Exception
     */
    public function destroy(int $id): RedirectResponse
    {
        $allChild = $this->skillRepository->getBy(
            Sendportal::currentWorkspaceId(),
            ['parent_id' => $id]
        );

        foreach ($allChild as $item) {
            $item->parent_id = 0;
            $item->save();
        }

        $this->skillRepository->destroy(Sendportal::currentWorkspaceId(), $id);
        return redirect()->route('sendportal.skills.index');
    }
}
