<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface InputOutputInventoryViewRepositoryInterface extends BaseRepositoryInterface
{
    public function findAllInventoriesPaginated(array $filters, int $limit, int $officeId, string $sort = null,
                                                array $columns = ['*']): LengthAwarePaginator;

    public function getDataReport(array $filters, int $officeId): Collection;
}