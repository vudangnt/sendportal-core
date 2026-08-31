<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Tags;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\TagStoreRequest;
use Sendportal\Base\Http\Requests\TagUpdateRequest;
use Sendportal\Base\Models\Tag;
use Sendportal\Base\Repositories\TagTenantRepository;
use Sendportal\Base\Tags\TagTree;

class TagsController extends Controller
{
    /** @var TagTenantRepository */
    private $tagRepository;

    /** @var TagTree */
    private $tagTree;

    public function __construct(TagTenantRepository $tagRepository, TagTree $tagTree)
    {
        $this->tagRepository = $tagRepository;
        $this->tagTree = $tagTree;
    }

    /**
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $workspaceId = Sendportal::currentWorkspaceId();

        // Chỉ render nhóm đang xem. Đổ hết ~3.039 tag ra một trang thì HTML lên 5MB và ô
        // tìm kiếm (lọc DOM phía client) phải lặp vài nghìn node mỗi phím gõ — spec §8.6.
        $tagGroups = $this->tagTree->rootsByDimension($workspaceId, 'children');
        $searchableCount = $this->tagTree->countSearchableRoots($workspaceId);

        $dimensions = array_keys($tagGroups);
        $dimension = (string) $request->query('dimension', $dimensions[0] ?? TagTree::DIMENSION_TIM_KIEM);

        // Tab Kỹ năng không render sẵn — tra qua TagSearchController.
        $tags = $dimension === TagTree::DIMENSION_TIM_KIEM ? [] : ($tagGroups[$dimension] ?? []);

        return view('sendportal::tags.index', compact('tags', 'tagGroups', 'searchableCount', 'dimension'));
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
