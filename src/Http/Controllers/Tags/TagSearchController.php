<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Tags;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;

/**
 * Tra tag theo từ khoá cho những dimension quá đông để render sẵn (SKILL: ~2.252 tag).
 *
 * Render sẵn thì HTML lên 5MB và mỗi phím gõ phải lặp vài nghìn node DOM — xem spec §8.6.
 */
class TagSearchController extends Controller
{
    private const DO_DAI_TOI_THIEU = 2;
    private const GIOI_HAN = 50;

    public function __invoke(Request $request): JsonResponse
    {
        $tuKhoa = trim((string) $request->query('q', ''));
        $dimension = trim((string) $request->query('dimension', ''));

        // Gõ 1 ký tự thì gần như khớp tất cả — trả rỗng thay vì bắt server quét vô ích.
        if (mb_strlen($tuKhoa) < self::DO_DAI_TOI_THIEU || $dimension === '') {
            return response()->json([]);
        }

        $workspaceId = Sendportal::currentWorkspaceId();

        $rows = DB::table('sendportal_tags as t')
            ->where('t.workspace_id', $workspaceId)
            ->where('t.dimension', $dimension)
            ->where('t.name', 'LIKE', '%' . $this->thoatLike($tuKhoa) . '%')
            ->selectRaw('t.id, t.name')
            ->selectRaw('(SELECT COUNT(DISTINCT ts.subscriber_id)
                            FROM sendportal_tag_subscriber ts
                            JOIN sendportal_subscribers s ON s.id = ts.subscriber_id
                           WHERE ts.tag_id = t.id
                             AND s.workspace_id = ?
                             AND s.unsubscribed_at IS NULL) AS subscribers', [$workspaceId])
            // Khớp từ đầu tên lên trước: gõ "java" thì "Java" phải đứng trên "Core Java".
            ->orderByRaw('CASE WHEN t.name LIKE ? THEN 0 ELSE 1 END', [$this->thoatLike($tuKhoa) . '%'])
            ->orderBy('t.name')
            ->limit(self::GIOI_HAN)
            ->get();

        return response()->json($rows);
    }

    /** `%` và `_` trong từ khoá phải được thoát, nếu không gõ "%" là quét toàn bảng. */
    private function thoatLike(string $s): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $s);
    }
}
