<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestRoomViewRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Enums\Lookups\StatusRequestLookup;
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

    public function countNewRequests(User $user): int
    {
        return $this->entity
            ->where('status_code', StatusRequestLookup::code(StatusRequestLookup::NEW))
            ->filterOfficeOrUser($user)
            ->count();
    }

    public function countApprovedRequests(User $user): int
    {
        return $this->entity
            ->where('status_code', StatusRequestLookup::code(StatusRequestLookup::APPROVED))
            ->filterOfficeOrUser($user)
            ->count();
    }

    public function countCancelledRequests(User $user): int
    {
        return $this->entity
            ->where('status_code', StatusRequestLookup::code(StatusRequestLookup::CANCELLED))
            ->filterOfficeOrUser($user)
            ->count();
    }

    public function countTotalRequests(User $user): int
    {
        return $this->entity
            ->filterOfficeOrUser($user)
            ->count();
    }
}