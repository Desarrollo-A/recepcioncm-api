<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @method Room create(array $data)
 * @method Room findById(int $id, array $columns = ['*'])
 * @method Room update(int $id, array $data)
 */
interface RoomRepositoryInterface extends BaseRepositoryInterface
{
    public function findAllPaginatedOffice(User $user, array $filters, int $limit, string $sort = null,
                                           array $columns = ['*']): LengthAwarePaginator;

    public function findAllByStateId(int $stateId, int $lookupActiveId): Collection;
}