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
use InvalidArgumentException;
use Sendportal\Base\Tags\Dimension;

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
        'name','parent_id','code','dimension','source'
    ];

    /** @var array */
    protected $withCount = [
        'subscribers','activeSubscribers'
    ];

    /**
     * Segment engine so số dimension đếm ở PHP với số đếm ở SQL. Hai phép chuẩn hoá đó
     * KHÔNG thể trùng nhau nếu cột chứa rác: PHP trim() strip \n\t\r còn SQL TRIM() chỉ
     * strip dấu cách; PHP strtoupper chỉ hiểu ASCII còn SQL UPPER hiểu Unicode dưới
     * collation utf8mb4_unicode_ci ('CAT' và 'CÁT' là một với SQL, là hai với PHP).
     *
     * Chốt ở model chứ không chỉ ở FormRequest, vì các lệnh import/backfill ghi thẳng vào
     * đây và không đi qua request nào. Ném lỗi chứ không im lặng sửa: một dimension lạ
     * nghĩa là gọi sai chỗ, và nuốt nó đi thì lỗi lộ ra ở tận lúc gửi campaign.
     */
    public function setDimensionAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['dimension'] = null;

            return;
        }

        $chuanHoa = strtoupper(trim((string) $value));

        if (!in_array($chuanHoa, Dimension::ALL, true)) {
            throw new InvalidArgumentException(
                'Dimension không hợp lệ: "' . $value . '". Phải nằm trong Dimension::ALL.'
            );
        }

        $this->attributes['dimension'] = $chuanHoa;
    }

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
