<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Tags;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\TagStoreRequest;
use Sendportal\Base\Http\Requests\TagUpdateRequest;
use Sendportal\Base\Models\Tag;
use Sendportal\Base\Repositories\TagTenantRepository;

class TagsController extends Controller
{
    /** @var TagTenantRepository */
    private $tagRepository;

    public function __construct(TagTenantRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @throws Exception
     */
    public function index(): View
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $tags = $this->tagRepository->all($workspaceId, 'name')->toArray();
        
        foreach ($tags as $key => $tag) {
            if ($tag['parent_id'] === 0) {
                foreach ($tags as $child) {
                    if ($child['parent_id'] === $tag['id']) {
                        $tags[$key]['children'][] = $child;
                    }
                }
                
                // Calculate total active subscribers count including child tags
                $tagModel = $this->tagRepository->find($workspaceId, $tag['id']);
                if ($tagModel) {
                    $tags[$key]['active_subscribers_count'] = $tagModel->total_active_subscribers_count;
                }
            }
        }
        
        // Hàm lọc
        $tags = array_filter($tags, function ($item) {
            return $item['parent_id'] === 0;
        });
        
        return view('sendportal::tags.index', compact('tags'));
    }

    public function create(): View
    {
        $parentTags = $this->tagRepository->getQueryBuilder(Sendportal::currentWorkspaceId())->where('parent_id', 0)->get();
        return view('sendportal::tags.create', compact('parentTags'));
    }

    /**
     * @throws Exception
     */
    public function store(TagStoreRequest $request): RedirectResponse
    {
        $this->tagRepository->store(Sendportal::currentWorkspaceId(), $request->all());
        return redirect()->route('sendportal.tags.index');
    }

    /**
     * @throws Exception
     */
    public function edit(int $id): View
    {
        $tag = $this->tagRepository->find(Sendportal::currentWorkspaceId(), $id, ['subscribers']);
        $parentTags = $this->tagRepository->getQueryBuilder(Sendportal::currentWorkspaceId())->where('parent_id', 0)->get();
        return view('sendportal::tags.edit', compact('tag', 'parentTags'));
    }

    /**
     * @throws Exception
     */
    public function update(int $id, TagUpdateRequest $request): RedirectResponse
    {
        $this->tagRepository->update(Sendportal::currentWorkspaceId(), $id, $request->all());

        return redirect()->route('sendportal.tags.index');
    }

    /**
     * @throws Exception
     */
    public function destroy(int $id): RedirectResponse
    {
        $allChild = $this->tagRepository->getBy(
            Sendportal::currentWorkspaceId(),
            ['parent_id' => $id]
        );

        foreach ($allChild as $item) {
            $item->parent_id = 0;
            $item->save();
        }

        $this->tagRepository->destroy(Sendportal::currentWorkspaceId(), $id);

        return redirect()->route('sendportal.tags.index');
    }
}
