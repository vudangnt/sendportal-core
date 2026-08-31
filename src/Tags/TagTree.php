<?php

declare(strict_types=1);

namespace Sendportal\Base\Tags;

use Illuminate\Support\Facades\DB;
use Sendportal\Base\Repositories\TagTenantRepository;

/**
 * Dựng cây tag gốc→con cho màn hình Tags và bước chọn người nhận của campaign.
 *
 * Trước đây hai controller tự dựng bằng vòng lặp LỒNG (O(n²)) và gọi
 * `find()` NGAY TRONG vòng lặp để lấy total_active_subscribers_count — một query
 * mỗi gốc. Đo local: 787 tag = 1,55s/585 query; 3.039 tag = 4,35s/2.837 query.
 * Task 1.10 đưa tag từ 787 lên ~3.039 nên chỗ này phải gọn lại trước.
 *
 * Ở đây: gom con một lượt (O(n)) và đếm subscriber cho TẤT CẢ gốc bằng MỘT query.
 */
final class TagTree
{
    /** Dimension không render sẵn mà nạp qua tìm kiếm. */
    public const DIMENSION_TIM_KIEM = 'SKILL';

    /** Nhóm cho tag chưa có dimension — phải có, nếu không chúng biến mất khỏi UI. */
    public const NHOM_KHAC = '_KHAC';

    public function __construct(private readonly TagTenantRepository $tags)
    {
    }

    /**
     * @param string $childKey tên khoá chứa con — view của Tags dùng 'children',
     *                         view campaign dùng 'child'. Giữ cả hai để không sửa view.
     * @return array<int, array>
     */
    public function roots(int $workspaceId, string $childKey = 'children'): array
    {
        $tatCa = $this->tags->all($workspaceId, 'name')->toArray();

        $conTheoCha = [];
        foreach ($tatCa as $tag) {
            $cha = (int) ($tag['parent_id'] ?? 0);
            if ($cha !== 0) {
                $conTheoCha[$cha][] = $tag;
            }
        }

        $demTheoGoc = $this->demSubscriberDangHoatDongTheoGoc($workspaceId);

        $goc = [];
        foreach ($tatCa as $tag) {
            if ((int) ($tag['parent_id'] ?? 0) !== 0) {
                continue;
            }

            $id = (int) $tag['id'];
            $tag[$childKey] = $conTheoCha[$id] ?? [];
            $tag['child_count'] = count($tag[$childKey]);
            $tag['active_subscribers_count'] = $demTheoGoc[$id] ?? 0;
            $goc[] = $tag;
        }

        return $goc;
    }

    /**
     * Cây gốc đã nhóm theo dimension, cho UI chia tab.
     *
     * KHÔNG gồm SKILL: sau Task 1.10 có ~2.252 tag SKILL, render sẵn thì HTML lên 5MB
     * và tìm kiếm phải lặp vài nghìn node DOM mỗi phím gõ (spec §8.6). Tab đó nạp qua
     * TagSearchController.
     *
     * Nhóm theo dimension THỰC CÓ chứ không hardcode danh sách: dimension mới sẽ tự có
     * tab thay vì rơi mất. Tag chưa có dimension (backfill bỏ qua vì trùng mã) vào nhóm
     * KHAC — không được để chúng biến mất khỏi màn chọn người nhận.
     *
     * @return array<string, array<int, array>>
     */
    public function rootsByDimension(int $workspaceId, string $childKey = 'child'): array
    {
        $nhom = [];

        foreach ($this->roots($workspaceId, $childKey) as $tag) {
            $dimension = trim((string) ($tag['dimension'] ?? ''));

            if ($dimension === self::DIMENSION_TIM_KIEM) {
                continue;
            }

            $nhom[$dimension === '' ? self::NHOM_KHAC : $dimension][] = $tag;
        }

        return $nhom;
    }

    /** Số tag gốc thuộc dimension nạp-qua-tìm-kiếm, để hiện lên nhãn tab. */
    public function countSearchableRoots(int $workspaceId): int
    {
        return DB::table('sendportal_tags')
            ->where('workspace_id', $workspaceId)
            ->where('dimension', self::DIMENSION_TIM_KIEM)
            ->where(fn ($q) => $q->whereNull('parent_id')->orWhere('parent_id', 0))
            ->count();
    }

    /**
     * Số subscriber đang hoạt động, tính DISTINCT trên gốc + TOÀN BỘ hậu duệ — cùng
     * ngữ nghĩa với Tag::getTotalActiveSubscribersCountAttribute(), vốn gom con bằng
     * đệ quy. Cây hiện sâu tới 3 tầng nên leo một cấp là SAI: đối chiếu trên dữ liệu
     * thật, tag 265 "Education / Training" ra 133 thay vì 320 vì bỏ sót cháu.
     *
     * Recursive CTE cho đúng ở mọi độ sâu mà vẫn một query cho tất cả gốc.
     *
     * @return array<int, int> root_id => số subscriber
     */
    private function demSubscriberDangHoatDongTheoGoc(int $workspaceId): array
    {
        $rows = DB::select(
            'WITH RECURSIVE cay AS (
                 SELECT id AS tag_id, id AS root_id
                   FROM sendportal_tags
                  WHERE workspace_id = ? AND (parent_id IS NULL OR parent_id = 0)
                 UNION ALL
                 SELECT t.id, c.root_id
                   FROM sendportal_tags t
                   JOIN cay c ON t.parent_id = c.tag_id
                  WHERE t.workspace_id = ?
             )
             SELECT c.root_id, COUNT(DISTINCT ts.subscriber_id) AS n
               FROM cay c
               JOIN sendportal_tag_subscriber ts ON ts.tag_id = c.tag_id
               JOIN sendportal_subscribers s ON s.id = ts.subscriber_id
              WHERE s.workspace_id = ? AND s.unsubscribed_at IS NULL
              GROUP BY c.root_id',
            [$workspaceId, $workspaceId, $workspaceId]
        );

        $dem = [];
        foreach ($rows as $r) {
            $dem[(int) $r->root_id] = (int) $r->n;
        }

        return $dem;
    }
}
