<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface MenuRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUserId(int $userId): Collection;

    public function findAllByRoleId(int $roleId): Collection;

    public function getPathRouteNavigationByUserId(int $userId): Collection;
}