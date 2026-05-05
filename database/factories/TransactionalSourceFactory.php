<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Models\TransactionalSource;

class TransactionalSourceFactory extends Factory
{
    /** @var string */
    protected $model = TransactionalSource::class;

    public function definition(): array
    {
        return [
            'hash' => $this->faker->uuid,
            'workspace_id' => Sendportal::currentWorkspaceId(),
            'request_payload' => ['test' => 'payload'],
        ];
    }
}
