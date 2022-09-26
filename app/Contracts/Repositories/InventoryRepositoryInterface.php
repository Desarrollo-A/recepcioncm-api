<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @method Inventory create(array $data)
 * @method Inventory update(int $id, array $data)
 * @method Inventory findById(int $id, array $columns = ['*'])
 */
interface InventoryRepositoryInterface extends BaseRepositoryInterface
{
    public function findAllPaginatedOffice(User $user, array $filters, int $limit, string $sort = null,
                                           array $columns = ['*']): LengthAwarePaginator;

    public function findAllByType(int $typeId, int $officeId): Collection;

    public function findAllSnacks(int $typeId, int $officeId): Collection;
}