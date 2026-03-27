<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Levels;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\LevelStoreRequest;
use Sendportal\Base\Http\Requests\LevelUpdateRequest;
use Sendportal\Base\Repositories\LevelTenantRepository;

class LevelsController extends Controller
{
    /** @var LevelTenantRepository */
    private $levelRepository;

    public function __construct(LevelTenantRepository $levelRepository)
    {
        $this->levelRepository = $levelRepository;
    }

    /**
     * @throws Exception
     */
    public function index(): View
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $levels = $this->levelRepository->all($workspaceId, 'name')->toArray();

        foreach ($levels as $key => $level) {
            if ($level['parent_id'] === 0) {
                foreach ($levels as $child) {
                    if ($child['parent_id'] === $level['id']) {
                        $levels[$key]['children'][] = $child;
                    }
                }

                $levelModel = $this->levelRepository->find($workspaceId, $level['id']);
                if ($levelModel) {
                    $levels[$key]['active_subscribers_count'] = $levelModel->total_active_subscribers_count;
                }
            }
        }

        $levels = array_filter($levels, function ($item) {
            return $item['parent_id'] === 0;
        });

        return view('sendportal::levels.index', compact('levels'));
    }

    public function create(): View
    {
        $parentLevels = $this->levelRepository->getQueryBuilder(Sendportal::currentWorkspaceId())
            ->where('parent_id', 0)->get();
        return view('sendportal::levels.create', compact('parentLevels'));
    }

    /**
     * @throws Exception
     */
    public function store(LevelStoreRequest $request): RedirectResponse
    {
        $this->levelRepository->store(Sendportal::currentWorkspaceId(), $request->all());
        return redirect()->route('sendportal.levels.index');
    }

    /**
     * @throws Exception
     */
    public function edit(int $id): View
    {
        $level = $this->levelRepository->find(Sendportal::currentWorkspaceId(), $id, ['subscribers']);
        $parentLevels = $this->levelRepository->getQueryBuilder(Sendportal::currentWorkspaceId())
            ->where('parent_id', 0)->get();
        return view('sendportal::levels.edit', compact('level', 'parentLevels'));
    }

    /**
     * @throws Exception
     */
    public function update(int $id, LevelUpdateRequest $request): RedirectResponse
    {
        $this->levelRepository->update(Sendportal::currentWorkspaceId(), $id, $request->all());
        return redirect()->route('sendportal.levels.index');
    }

    /**
     * @throws Exception
     */
    public function destroy(int $id): RedirectResponse
    {
        $allChild = $this->levelRepository->getBy(
            Sendportal::currentWorkspaceId(),
            ['parent_id' => $id]
        );

        foreach ($allChild as $item) {
            $item->parent_id = 0;
            $item->save();
        }

        $this->levelRepository->destroy(Sendportal::currentWorkspaceId(), $id);
        return redirect()->route('sendportal.levels.index');
    }
}
