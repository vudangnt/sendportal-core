<?php

declare(strict_types=1);

namespace Sendportal\Base\Models;

use Carbon\Carbon;
use Database\Factories\TemplateFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $workspace_id
 * @property string $name
 * @property string|null $code
 * @property string|null $subject
 * @property string|null $content
 * @property string $kind
 * @property bool $is_default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property EloquentCollection $campaigns
 *
 * @method static TemplateFactory factory
 */
class Template extends BaseModel
{
    use HasFactory;

    public const KIND_CAMPAIGN      = 'campaign';
    public const KIND_TRANSACTIONAL = 'transactional';

    /** @var array<int,string> */
    public static array $kinds = [self::KIND_CAMPAIGN, self::KIND_TRANSACTIONAL];

    protected static function newFactory()
    {
        return TemplateFactory::new();
    }

    /** @var string */
    protected $table = 'sendportal_templates';

    /** @var array */
    protected $guarded = [];

    /** @var array */
    protected $casts = [
        'is_default' => 'bool',
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function isInUse(): bool
    {
        return $this->campaigns()->count() > 0;
    }

    public function scopeKind(Builder $q, string $kind): Builder
    {
        return $q->where('kind', $kind);
    }

    public function scopeCampaign(Builder $q): Builder
    {
        return $q->where('kind', self::KIND_CAMPAIGN);
    }

    public function scopeTransactional(Builder $q): Builder
    {
        return $q->where('kind', self::KIND_TRANSACTIONAL);
    }
}
