<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Campaigns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Segments\EmptySegmentRuleException;
use Sendportal\Base\Segments\SegmentResolver;
use Sendportal\Base\Segments\SegmentRule;
use Sendportal\Base\Segments\SegmentRuleMismatchException;

/**
 * Đếm người nhận cho bộ tag đang tick, để preview hiện số thật trước khi gửi.
 */
class CampaignRecipientCountController extends Controller
{
    public function __invoke(Request $request, SegmentResolver $resolver): JsonResponse
    {
        // Chặn ba thứ cùng lúc: quét cả bảng pivot 315k dòng bằng cách gửi rất nhiều
        // tag, whereIn phình ra hàng chục nghìn placeholder, và `tags[0][]=5` khiến
        // intval(array) trả 1 rồi âm thầm đếm theo tag id 1.
        $request->validate([
            'tags' => 'array|max:200',
            'tags.*' => 'integer',
        ]);

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
        } catch (SegmentRuleMismatchException $e) {
            // Khác accessor: ở đây marketer đang đứng trước màn hình. Trả 0 trần là
            // đưa họ một con số trông hợp lệ rồi để họ bấm Gửi — nói thẳng là chưa
            // đếm được thì hơn.
            Log::error('- Endpoint đếm người nhận gặp rule lệch. workspace_id=' . $workspaceId
                . ' loi=' . $e->getMessage());

            return response()->json([
                'count' => 0,
                'rule' => '',
                'empty' => true,
                'error' => __('Bộ tag đang chọn có vấn đề, chưa đếm được. Báo kỹ thuật.'),
            ]);
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
