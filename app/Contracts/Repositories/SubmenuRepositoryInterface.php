<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\Submenu;
use Illuminate\Database\Eloquent\Collection;

interface SubmenuRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUserId(int $userId): Collection;

    public function findByPathRouteAndMenuId(string $path, int $menuId): Submenu;
}