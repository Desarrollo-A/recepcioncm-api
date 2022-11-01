<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\RequestEmail;

/**
 * @method RequestEmail create(array $data)
 * @method RequestEmail update(int $id, array $data)
 */
interface RequestEmailRepositoryInterface extends BaseRepositoryInterface
{
    public function bulkInsert(array $data): bool;
}