<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RequestPackageViewRepositoryInterface extends BaseRepositoryInterface
{
    public function findAllPackagesPaginated(array $filters, int $limit, User $user, string $sort = null,
                                          array $columns = ['*']): LengthAwarePaginator;

    public function findAllByDriverIdPaginated(array $filters, int $limit, User $user, string $sort = null,
                                                       array $columns = ['*']): LengthAwarePaginator;

    public function findAllDeliveredByDriverIdPaginated(array $filters, int $limit, User $user, string $sort = null,
                                                        array $columns = ['*']): LengthAwarePaginator;

    public function getDataReport (array $filters, int $driverId): Collection;

    public function findAllPackagesByManagerIdPaginated(
        array $filters, int $limit, int $departmentManagerId, string $sort = null,
        array $columns = ['*']
    ): LengthAwarePaginator;
}