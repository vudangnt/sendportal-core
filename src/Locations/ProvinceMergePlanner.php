<?php

declare(strict_types=1);

namespace Sendportal\Base\Locations;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tính kế hoạch ghép location (63 -> 34) cho 1 workspace. Chỉ ĐỌC DB.
 *
 * Plan:
 *  - renames:      [{id, from, to}]
 *  - merges:       [{from:{id,name,subs}, to:{id,name}, move:[subscriber_id], drop:[subscriber_id], subscriberRows:int}]
 *  - variants:     [{id, name, subs, province}]  (chuẩn hoá trùng tên mới nhưng không phải dòng giữ -> giữ nguyên)
 *  - unrecognized: [{id, name, subs}]
 *  - skipped:      [{id, name, subs, province, reason}]  (thiếu dòng giữ / xung đột tên)
 *  - keptIds:      id các dòng giữ có liên quan
 *  - campaignRefs: số dòng campaign_location trỏ tới location bị ghép
 */
final class ProvinceMergePlanner
{
    /** @var ProvinceMergeMap */
    private $map;

    public function __construct(ProvinceMergeMap $map)
    {
        $this->map = $map;
    }

    public function build(int $workspaceId): array
    {
        $rows = DB::table('sendportal_locations')->where('workspace_id', $workspaceId)->orderBy('id')->get(['id', 'name']);
        $subCounts = DB::table('sendportal_location_subscriber')->select('location_id', DB::raw('COUNT(*) AS c'))
            ->groupBy('location_id')->pluck('c', 'location_id');
        $allNames = DB::table('sendportal_locations')->pluck('id', 'name')->all();

        $plan = ['renames' => [], 'merges' => [], 'variants' => [], 'unrecognized' => [], 'skipped' => [],
            'keptIds' => [], 'campaignRefs' => 0];

        $byProvince = [];
        foreach ($rows as $row) {
            $row = (object) ['id' => (int) $row->id, 'name' => (string) $row->name, 'subs' => (int) ($subCounts[$row->id] ?? 0)];
            $province = $this->map->province($row->name);
            if ($province === null) {
                $plan['unrecognized'][] = (array) $row;
                continue;
            }
            $byProvince[$province['code']]['province'] = $province;
            $byProvince[$province['code']]['rows'][] = $row;
        }

        foreach ($byProvince as $group) {
            $this->planProvince($plan, $group['province'], $group['rows'], $allNames);
        }

        $mergedIds = array_map(function ($m) {
            return $m['from']['id'];
        }, $plan['merges']);
        if ($mergedIds !== [] && Schema::hasTable('sendportal_campaign_location')) {
            $plan['campaignRefs'] = DB::table('sendportal_campaign_location')->whereIn('location_id', $mergedIds)->count();
        }
        $plan['keptIds'] = array_values(array_unique($plan['keptIds']));

        return $plan;
    }

    /** Có gì cần ghi không (rename hoặc pivot/subscribers thật sự cần đổi). */
    public static function hasChanges(array $plan): bool
    {
        if ($plan['renames'] !== []) {
            return true;
        }
        foreach ($plan['merges'] as $m) {
            if ($m['move'] !== [] || $m['drop'] !== [] || $m['subscriberRows'] > 0) {
                return true;
            }
        }

        return false;
    }

    private function planProvince(array &$plan, array $province, array $rows, array $allNames): void
    {
        $newName = $province['name'];
        $oldFirst = $province['old'][0];

        $kept = null;
        foreach ($rows as $row) {
            if (trim($row->name) === $newName) {
                $kept = $row;
                break;
            }
        }
        $renameTo = null;
        if ($kept === null && $oldFirst !== $newName) {
            foreach ($rows as $row) {
                if (trim($row->name) === $oldFirst) {
                    $kept = $row;
                    $renameTo = $newName;
                    break;
                }
            }
        }
        if ($renameTo !== null) {
            if (isset($allNames[$newName])) {
                $plan['skipped'][] = ['id' => $kept->id, 'name' => $kept->name, 'subs' => $kept->subs,
                    'province' => $newName, 'reason' => 'xung đột tên: "' . $newName . '" đã tồn tại ở workspace khác'];
                $kept = null;
            } else {
                $plan['renames'][] = ['id' => $kept->id, 'from' => $kept->name, 'to' => $newName];
            }
        }

        $keptSubs = $kept === null ? [] : $this->subscriberIds($kept->id);
        if ($kept !== null) {
            $plan['keptIds'][] = $kept->id;
        }

        foreach ($rows as $row) {
            if ($kept !== null && $row->id === $kept->id) {
                continue;
            }
            $trimmed = trim($row->name);
            $isOldFirstDuplicate = $kept !== null && $oldFirst !== $newName && $trimmed === $oldFirst;
            if (! ($this->map->isMergedAway($row->name) || $isOldFirstDuplicate)) {
                $plan['variants'][] = ['id' => $row->id, 'name' => $row->name, 'subs' => $row->subs, 'province' => $newName];
                continue;
            }
            if ($kept === null) {
                if (! $this->isSkipped($plan, $row->id)) {
                    $plan['skipped'][] = ['id' => $row->id, 'name' => $row->name, 'subs' => $row->subs,
                        'province' => $newName, 'reason' => 'thiếu dòng giữ "' . $newName . '"'];
                }
                continue;
            }
            $plan['merges'][] = $this->planMerge($row, $kept, $keptSubs);
        }
    }

    private function planMerge($from, $kept, array &$keptSubs): array
    {
        $move = [];
        $drop = [];
        foreach (array_keys($this->subscriberIds($from->id)) as $sid) {
            if (isset($keptSubs[$sid])) {
                $drop[] = $sid;
                continue;
            }
            $keptSubs[$sid] = true;
            $move[] = $sid;
        }

        return [
            'from' => ['id' => $from->id, 'name' => $from->name, 'subs' => $from->subs],
            'to' => ['id' => $kept->id, 'name' => $kept->name],
            'move' => $move,
            'drop' => $drop,
            'subscriberRows' => DB::table('sendportal_subscribers')->where('location_id', $from->id)->count(),
        ];
    }

    /** @return array<int, true> set các subscriber_id */
    private function subscriberIds(int $locationId): array
    {
        $set = [];
        foreach (DB::table('sendportal_location_subscriber')->where('location_id', $locationId)->orderBy('id')->pluck('subscriber_id') as $sid) {
            $set[(int) $sid] = true;
        }

        return $set;
    }

    private function isSkipped(array $plan, int $id): bool
    {
        foreach ($plan['skipped'] as $s) {
            if ($s['id'] === $id) {
                return true;
            }
        }

        return false;
    }
}
