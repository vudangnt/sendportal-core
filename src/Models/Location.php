<?php

namespace Sendportal\Base\Models;

use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
}
