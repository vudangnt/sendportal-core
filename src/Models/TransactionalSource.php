<?php

declare(strict_types=1);

namespace Sendportal\Base\Models;

use Carbon\Carbon;
use Database\Factories\TransactionalSourceFactory;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Ramsey\Uuid\Uuid;

/**
 * @property int $id
 * @property string $hash
 * @property int $workspace_id
 * @property array|null $request_payload
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read EloquentCollection $messages
 *
 * @method static TransactionalSourceFactory factory
 */
class TransactionalSource extends BaseModel
{
    use HasFactory;

    // NOTE: we require this because of namespace issues when resolving factories from models
    // not in the default `App\Models` namespace.
    protected static function newFactory()
    {
        return TransactionalSourceFactory::new();
    }

    protected $table = 'sendportal_transactional_sources';

    protected $guarded = [];

    protected $casts = [
        'request_payload' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->hash = $model->hash ?: Uuid::uuid4()->toString();
        });
    }

    public function messages(): MorphMany
    {
        return $this->morphMany(Message::class, 'source');
    }
}
