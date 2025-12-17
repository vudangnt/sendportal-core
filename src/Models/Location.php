<?php

namespace Sendportal\Base\Models;

use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Location extends BaseModel
{
    use HasFactory;

    // NOTE(david): we require this because of namespace issues when resolving factories from models
    // not in the default `App\Models` namespace.
    protected static function newFactory()
    {
        return LocationFactory::new();
    }

    /** @var string */
    protected $table = 'sendportal_locations';

    /** @var array */
    protected $fillable = [
        'name','parent_id', 'code', 'type'
    ];

    /** @var array */
    protected $withCount = [
        'subscribers','activeSubscribers'
    ];

    /**
     * Subscribers in this tag.
     */
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'sendportal_location_subscriber')->withTimestamps();
    }


    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'sendportal_campaign_location');
    }


    /**
     * Active subscribers in this tag.
     */
    public function activeSubscribers(): BelongsToMany
    {
        return $this->subscribers()
            ->whereNull('unsubscribed_at')
            ->withTimestamps();
    }

    /**
     * Subscribers in this tag.
     */
    public function child(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    /**
     * Get total active subscribers count including child locations (without duplicates).
     * 
     * @return int
     */
    public function getTotalActiveSubscribersCountAttribute(): int
    {
        // Get all child location IDs recursively
        $childLocationIds = $this->getAllChildLocationIds();
        
        // Get all location IDs (parent + children)
        $allLocationIds = array_merge([$this->id], $childLocationIds);
        
        // If no child locations, just return the direct count
        if (empty($childLocationIds)) {
            return $this->active_subscribers_count ?? 0;
        }
        
        // Count unique active subscribers across all locations using subquery to avoid duplicates
        // This ensures each subscriber is counted only once even if they have multiple locations
        // Using subquery with distinct is more efficient than loading all records
        $uniqueSubscriberIds = DB::table('sendportal_location_subscriber')
            ->whereIn('location_id', $allLocationIds)
            ->distinct()
            ->pluck('subscriber_id');
        
        if ($uniqueSubscriberIds->isEmpty()) {
            return 0;
        }
        
        // Count only active subscribers from the unique list
        return Subscriber::whereIn('id', $uniqueSubscriberIds)
            ->whereNull('unsubscribed_at')
            ->where('workspace_id', $this->workspace_id)
            ->count();
    }

    /**
     * Get all child location IDs recursively.
     * 
     * @return array
     */
    protected function getAllChildLocationIds(): array
    {
        $childLocationIds = [];
        
        $children = $this->child()->get();
        
        foreach ($children as $child) {
            $childLocationIds[] = $child->id;
            // Recursively get grandchildren
            $childLocationIds = array_merge($childLocationIds, $child->getAllChildLocationIds());
        }
        
        return $childLocationIds;
    }
}
