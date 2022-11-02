<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\RequestEmail;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method RequestEmail create(array $data)
 * @method RequestEmail update(int $id, array $data)
 */
interface RequestEmailRepositoryInterface extends BaseRepositoryInterface
{
    public function bulkInsert(array $data): bool;

    public function findByRequestId(int $requestId, array $columns = ['*']): Collection;
}