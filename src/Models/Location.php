<?php

namespace Sendportal\Base\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends BaseModel
{
    use HasFactory;

    // NOTE(david): we require this because of namespace issues when resolving factories from models
    // not in the default `App\Models` namespace.
    protected static function newFactory()
    {
        return TagFactory::new();
    }

    /** @var string */
    protected $table = 'locations';

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
}
