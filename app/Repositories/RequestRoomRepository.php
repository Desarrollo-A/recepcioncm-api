<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestRoomRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\RequestRoom;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class RequestRoomRepository extends BaseRepository implements RequestRoomRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|RequestRoom
     */
    protected $entity;

    public function __construct(RequestRoom $requestRoom)
    {
        $this->entity = $requestRoom;
    }

    public function findById(int $id, array $columns = ['*']): RequestRoom
    {
        return $this->entity
            ->with([
                'request',
                'request.status',
                'request.type',
                'request.user',
                'request.inventories',
                'request.cancelRequest',
                'request.cancelRequest.user',
                'request.proposalRequest',
                'room',
                'room.office',
                'level'
            ])
            ->findOrFail($id, $columns);
    }
}