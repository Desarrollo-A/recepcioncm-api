<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface InputOutputInventoryViewRepositoryInterface extends BaseRepositoryInterface
{
    public function findAllInventoriesPaginated(array $filters, int $limit, int $officeId, string $sort = null,
                                                array $columns = ['*']): LengthAwarePaginator;
}