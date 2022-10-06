<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestRoomRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Enums\Lookups\StatusRequestLookup;
use App\Models\RequestRoom;
use App\Models\User;
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

    public function getDataCalendar(User $user)
    {
        return $this->entity
            ->with(['request', 'request.type', 'room', 'level'])
            ->join('rooms', 'rooms.id', '=', 'request_room.room_id')
            ->join('requests', 'requests.id', '=', 'request_room.request_id')
            ->join('lookups AS st', 'st.id', '=', 'requests.status_id')
            ->where('st.code', StatusRequestLookup::code(StatusRequestLookup::APPROVED))
            ->whereDate('requests.start_date', '>=', now()->startOfDay())
            ->filterOfficeOrUser($user)
            ->get()
            ->map(function ($requestRoom) {
                info($requestRoom);
                return (object)[
                    'title' => "Título: {$requestRoom->request->title}. Sala {$requestRoom->room->name} - {$requestRoom->level->name}",
                    'request' => $requestRoom->request
                ];
            });
    }
}