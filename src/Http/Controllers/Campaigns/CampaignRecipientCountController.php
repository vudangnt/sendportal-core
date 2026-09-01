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
        // Trần 3.000, KHÔNG phải 200. 200 là con số phòng thủ đặt bừa cho whereIn, mà
        // LIST có 211 tag gốc và CAT có 225: bấm "All" ở một tab bất kỳ là dính 422, banner
        // báo "Không tính được số người nhận" trong khi nút Gửi vẫn bấm được — còn đường
        // gửi thì nuốt trọn cả 2.251 tag SKILL (3.820 người nhận, 1,5s). Preview chặt hơn
        // đường gửi là preview nói dối. 3.000 vẫn nằm rất xa trần 65.535 placeholder của
        // MySQL, nên vế "whereIn phình ra" vẫn được giữ.
        //
        // `tags.*` integer vẫn phải có: `tags[0][]=5` khiến intval(array) trả 1 rồi âm thầm
        // đếm theo tag id 1. Throttle 30/phút ở route giữ vế quét bảng pivot 315k dòng.
        $request->validate([
            'tags' => 'array|max:3000',
            'tags.*' => 'integer',
        ]);

        // PHP cat POST o `max_input_vars` (mac dinh 1000; php-fpm cua prod dang de mac
        // dinh) va KHONG bao mot tieng nao: gui 2.251 tag thi $_POST chi con 1.000, endpoint
        // van tra ve mot con so trong rat hop le. Do chinh la "so sai trong hop ly" — thu
        // ma toan bo thiet ke nay ton tai de tranh. Voi tran 200 cu thi phan du bi 422 chan
        // lai; noi tran len 3.000 la mo dung khe do ra, nen phai tu bat lay.
        // Doi chieu so `tags[]` trong body tho voi so da parse: lech nghia la bi cat.
        $body = (string) $request->getContent();
        $soTrongBody = substr_count($body, 'tags[]=') + substr_count($body, 'tags%5B%5D=');
        $soDaParse = count((array) $request->input('tags', []));

        if ($soTrongBody > $soDaParse) {
            Log::error('- POST bi max_input_vars cat bot tag. gui=' . $soTrongBody
                . ' nhan=' . $soDaParse . ' max_input_vars=' . ini_get('max_input_vars'));

            return response()->json([
                'count' => 0,
                'rule' => '',
                'empty' => true,
                'error' => __('Chọn quá nhiều tag nên máy chủ cắt bớt danh sách — chưa đếm được. Bỏ bớt tag rồi thử lại.'),
            ]);
        }

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
