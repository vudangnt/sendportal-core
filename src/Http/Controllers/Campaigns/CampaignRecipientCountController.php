<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Campaigns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Segments\EmptySegmentRuleException;
use Sendportal\Base\Segments\SegmentResolver;
use Sendportal\Base\Segments\SegmentRule;

/**
 * Đếm người nhận cho bộ tag đang tick, để preview hiện số thật trước khi gửi.
 */
class CampaignRecipientCountController extends Controller
{
    public function __invoke(Request $request, SegmentResolver $resolver): JsonResponse
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $tagIds = array_filter(array_map('intval', (array) $request->input('tags', [])));

        if ($tagIds === []) {
            return response()->json(['count' => 0, 'rule' => '', 'empty' => true]);
        }

        // DB::table chứ không dùng model Tag: Tag có $withCount nên Eloquent chèn thêm
        // subquery vào SELECT, thừa cho việc chỉ cần id + dimension + name.
        $tags = DB::table('sendportal_tags')
            ->where('workspace_id', $workspaceId)
            ->whereIn('id', $tagIds)
            ->get(['id', 'name', 'dimension']);

        $rule = SegmentRule::fromTags($tags);

        try {
            $count = $resolver->count($workspaceId, $rule);
        } catch (EmptySegmentRuleException) {
            return response()->json(['count' => 0, 'rule' => '', 'empty' => true]);
        }

        return response()->json([
            'count' => $count,
            'rule' => $this->moTaRule($tags, $rule),
            'empty' => false,
        ]);
    }

    /** Rule bằng chữ: "(Java HOẶC Python) VÀ (HCM)" */
    private function moTaRule($tags, SegmentRule $rule): string
    {
        $tenTheoId = [];
        foreach ($tags as $t) {
            $tenTheoId[(int) $t->id] = (string) $t->name;
        }

        $ve = [];
        foreach ($rule->groups() as $ids) {
            $ten = array_map(static fn ($id) => $tenTheoId[$id] ?? (string) $id, $ids);
            $ve[] = '(' . implode(' HOẶC ', $ten) . ')';
        }

        return implode(' VÀ ', $ve);
    }
}
