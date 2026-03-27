<?php

declare(strict_types=1);

namespace Sendportal\Base\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Level extends BaseModel
{
    protected $table = 'sendportal_levels';

    protected $fillable = [
        'name', 'parent_id'
    ];

    protected $withCount = [
        'subscribers', 'activeSubscribers'
    ];

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'sendportal_campaign_level');
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'sendportal_level_subscriber')->withTimestamps();
    }

    public function activeSubscribers(): BelongsToMany
    {
        return $this->subscribers()
            ->whereNull('unsubscribed_at')
            ->withTimestamps();
    }

    public function child(): HasMany
    {
        return $this->hasMany(Level::class, 'parent_id');
    }

    public function getTotalActiveSubscribersCountAttribute(): int
    {
        $childIds = $this->getAllChildIds();
        $allIds = array_merge([$this->id], $childIds);

        if (empty($childIds)) {
            return $this->active_subscribers_count ?? 0;
        }

        $uniqueSubscriberIds = DB::table('sendportal_level_subscriber')
            ->whereIn('level_id', $allIds)
            ->distinct()
            ->pluck('subscriber_id');

        if ($uniqueSubscriberIds->isEmpty()) {
            return 0;
        }

        return Subscriber::whereIn('id', $uniqueSubscriberIds)
            ->whereNull('unsubscribed_at')
            ->where('workspace_id', $this->workspace_id)
            ->count();
    }

    protected function getAllChildIds(): array
    {
        $childIds = [];
        $children = $this->child()->get();
        foreach ($children as $child) {
            $childIds[] = $child->id;
            $childIds = array_merge($childIds, $child->getAllChildIds());
        }
        return $childIds;
    }
}
