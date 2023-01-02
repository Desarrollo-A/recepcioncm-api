<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestRoomViewRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Enums\Lookups\StatusRoomRequestLookup;
use App\Models\RequestRoomView;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

class RequestRoomViewRepository extends BaseRepository implements RequestRoomViewRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|RequestRoomView
     */
    protected $entity;

    public function __construct(RequestRoomView $requestRoomView)
    {
        $this->entity = $requestRoomView;
    }

    public function findAllRoomsPaginated(array $filters, int $limit, User $user, string $sort = null,
                                          array $columns = ['*']): LengthAwarePaginator
    {
        return $this->entity
            ->filter($filters)
            ->filterOfficeOrUser($user)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }
}