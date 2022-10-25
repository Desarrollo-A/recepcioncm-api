<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface RequestRoomViewRepositoryInterface extends BaseRepositoryInterface
{
    public function findAllRoomsPaginated(array $filters, int $limit, User $user, string $sort = null,
                                          array $columns = ['*']): LengthAwarePaginator;

    public function countNewRequests(User $user): int;

    public function countApprovedRequests(User $user): int;

    public function countCancelledRequests(User $user): int;

    public function countTotalRequests(User $user): int;
}