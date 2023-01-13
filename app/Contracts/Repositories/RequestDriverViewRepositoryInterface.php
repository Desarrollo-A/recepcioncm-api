<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface RequestDriverViewRepositoryInterface extends BaseRepositoryInterface
{
    public function findAllDriversPaginated(array $filters, int $limit, User $user, string $sort = null,
                                             array $columns = ['*']): LengthAwarePaginator;

    public function findAllByDriverIdPaginated(array $filters, int $limit, User $user, string $sort = null,
                                            array $columns = ['*']): LengthAwarePaginator;
}