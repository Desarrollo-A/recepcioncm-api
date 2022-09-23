<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\User;

/**
 * @method User findById(int $id, array $columns = ['*'])
 */
interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByNoEmployee(string $noEmployee): User;
}