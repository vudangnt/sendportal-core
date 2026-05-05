<?php

declare(strict_types=1);

namespace Sendportal\Base\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Ramsey\Uuid\Uuid;

class TransactionalSource extends BaseModel
{
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
