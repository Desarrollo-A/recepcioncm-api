<?php

namespace App\Repositories;

use App\Contracts\Repositories\RoomRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

class RoomRepository extends BaseRepository implements RoomRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|Room
     */
    protected $entity;

    public function __construct(Room $room)
    {
        $this->entity = $room;
    }

    public function findAllPaginatedOffice(User $user, array $filters, int $limit, string $sort = null,
                                           array $columns = ['*']): LengthAwarePaginator
    {
        return $this->entity
            ->with('status')
            ->orWhereHas('status', function ($query) use ($filters) {
                $query->filter($filters);
            })
            ->filter($filters)
            ->filterOffice($user)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }

    public function findById(int $id, array $columns = ['*']): Room
    {
        return $this->entity
            ->with('status', 'office')
            ->findOrFail($id, $columns);
    }

    public function findAllByStateId(int $stateId, int $lookupActiveId): Collection
    {
        return $this->entity
            ->select(['rooms.*'])
            ->with('office')
            ->join('offices', 'rooms.office_id', '=', 'offices.id')
            ->where('offices.state_id', $stateId)
            ->where('rooms.status_id', $lookupActiveId)
            ->get();
    }
}