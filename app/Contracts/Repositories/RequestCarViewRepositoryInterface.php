<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface RequestCarViewRepositoryInterface extends BaseRepositoryInterface
{
    public function findAllRequestsCarPaginated(array $filters, int $limit, User $user, string $sort = null,
                                                array $columns = ['*']): LengthAwarePaginator;
}