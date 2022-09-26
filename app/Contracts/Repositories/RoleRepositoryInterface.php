<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\Role;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function findByName(string $name): Role;
}