<?php

declare(strict_types=1);

namespace Sendportal\Base\Models;

use Carbon\Carbon;
use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property int $workspace_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property EloquentCollection $campaigns
 * @property EloquentCollection $subscribers
 * @property EloquentCollection $active_subscribers
 *
 * @method static TagFactory factory
 */
class Tag extends BaseModel
{
    use HasFactory;

    // NOTE(david): we require this because of namespace issues when resolving factories from models
    // not in the default `App\Models` namespace.
    protected static function newFactory()
    {
        return TagFactory::new();
    }

    /** @var string */
    protected $table = 'sendportal_tags';

    /** @var array */
    protected $fillable = [
        'name','parent_id'
    ];

    /** @var array */
    protected $withCount = [
        'subscribers','activeSubscribers'
    ];

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'sendportal_campaign_tag');
    }

    /**
     * Subscribers in this tag.
     */
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'sendportal_tag_subscriber')->withTimestamps();
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
        return $this->hasMany(Tag::class, 'parent_id');
    }

    /**
     * Get total active subscribers count including child tags (without duplicates).
     * 
     * @return int
     */
    public function getTotalActiveSubscribersCountAttribute(): int
    {
        // Get all child tag IDs recursively
        $childTagIds = $this->getAllChildTagIds();
        
        // Get all tag IDs (parent + children)
        $allTagIds = array_merge([$this->id], $childTagIds);
        
        // If no child tags, just return the direct count
        if (empty($childTagIds)) {
            return $this->active_subscribers_count ?? 0;
        }
        
        // Count unique active subscribers across all tags using subquery to avoid duplicates
        // This ensures each subscriber is counted only once even if they have multiple tags
        // Using subquery with distinct is more efficient than loading all records
        $uniqueSubscriberIds = DB::table('sendportal_tag_subscriber')
            ->whereIn('tag_id', $allTagIds)
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
     * Get all child tag IDs recursively.
     * 
     * @return array
     */
    protected function getAllChildTagIds(): array
    {
        $childTagIds = [];
        
        $children = $this->child()->get();
        
        foreach ($children as $child) {
            $childTagIds[] = $child->id;
            // Recursively get grandchildren
            $childTagIds = array_merge($childTagIds, $child->getAllChildTagIds());
        }
        
        return $childTagIds;
    }
}
