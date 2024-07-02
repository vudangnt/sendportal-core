<?php

declare(strict_types=1);

namespace Sendportal\Base\Repositories;

use App\Models\User;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Sendportal\Base\Repositories\BaseEloquentRepository;

class UsersRepository extends BaseEloquentRepository
{
    /** @var string */
    protected $modelName = User::class;

}
