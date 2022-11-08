<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestRoomRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Enums\Lookups\StatusRequestLookup;
use App\Models\RequestRoom;
use App\Models\User;
use Carbon\Carbon;
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
                'request.requestPhoneNumber',
                'request.requestEmail',
                'request.inventories',
                'request.cancelRequest',
                'request.cancelRequest.user',
                'request.proposalRequest',
                'request.score',
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
                return (object)[
                    'title' => "Título: {$requestRoom->request->title}. Sala {$requestRoom->room->name} - {$requestRoom->level->name}",
                    'request' => $requestRoom->request
                ];
            });
    }

    public function getSummaryOfDay(User $user)
    {
        return $this->entity
            ->with(['request', 'request.type', 'room'])
            ->join('rooms', 'rooms.id', '=', 'request_room.room_id')
            ->join('requests', 'requests.id', '=', 'request_room.request_id')
            ->join('lookups AS st', 'st.id', '=', 'requests.status_id')
            ->where('st.code', StatusRequestLookup::code(StatusRequestLookup::APPROVED))
            ->whereDate('requests.start_date', now())
            ->filterOfficeOrUser($user)
            ->orderBy('requests.start_date', 'ASC')
            ->get()
            ->map(function ($requestRoom) {
                $startDate = new Carbon($requestRoom->request->start_date);
                $endDate = new Carbon($requestRoom->request->end_date);
                return (object)[
                    'title' => $requestRoom->request->title,
                    'subtitle' => "{$startDate->format('g:i A')} - {$endDate->format('g:i A')}, Sala {$requestRoom->room->name}",
                    'request' => $requestRoom->request
                ];
            });
    }
}