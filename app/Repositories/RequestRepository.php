<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Enums\Lookups\StatusPackageRequestLookup;
use App\Models\Enums\Lookups\StatusRoomRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Request;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class RequestRepository extends BaseRepository implements RequestRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|Request
     */
    protected $entity;

    public function __construct(Request $request)
    {
        $this->entity = $request;
    }

    public function findById(int $id, array $columns = ['*']): Request
    {
        return $this->entity
            ->with(['type', 'status'])
            ->findOrFail($id, $columns);
    }

    public function isAvailableSchedule(Carbon $startDate, Carbon $endDate): bool
    {
        $requests = $this->entity
            ->join('lookups', 'lookups.id', '=', 'requests.status_id')
            ->where('start_date', '>=', $startDate)
            ->where('end_date', '<=', $endDate)
            ->whereIn('lookups.code', [StatusRoomRequestLookup::code(StatusRoomRequestLookup::APPROVED),
                StatusRoomRequestLookup::code(StatusRoomRequestLookup::PROPOSAL)])
            ->count();
        return $requests === 0;
    }

    public function roomsSetAsideByDay(Carbon $date): Collection
    {
        return $this->entity
            ->with('status')
            ->join('lookups', 'lookups.id', '=', 'requests.status_id')
            ->whereDate('start_date', $date)
            ->whereIn('lookups.code', [StatusRoomRequestLookup::code(StatusRoomRequestLookup::APPROVED),
                StatusRoomRequestLookup::code(StatusRoomRequestLookup::IN_REVIEW)])
            ->get();
    }

    public function getPreviouslyByCode(string $code, array $columns = ['*']): Collection
    {
        return $this->entity
            ->join('lookups', 'lookups.id', '=', 'requests.status_id')
            ->whereDate('end_date', '<', now())
            ->where('lookups.code', $code)
            ->get($columns);
    }

    public function getExpired(array $columns = ['*']): Collection
    {
        return $this->entity
            ->join('lookups', 'lookups.id', '=', 'requests.status_id')
            ->whereDate('end_date', '<', now())
            ->expired()
            ->get($columns);
    }

    /**
     * @return void
     */
    public function bulkStatusUpdate(array $ids, int $statusId)
    {
        $this->entity
            ->whereIn('id', $ids)
            ->update(['status_id' => $statusId]);
    }

    public function getApprovedRequestsTomorrow(): Collection
    {
        return $this->entity
            ->select(['requests.*'])
            ->join('lookups AS status', 'status.id', '=', 'requests.status_id')
            ->join('lookups AS type', 'type.id', '=', 'requests.type_id')
            ->whereDate('start_date', now()->addDay())
            ->where('status.code', StatusRoomRequestLookup::code(StatusRoomRequestLookup::APPROVED))
            ->whereIn('type.code', [TypeRequestLookup::code(TypeRequestLookup::ROOM),
                TypeRequestLookup::code(TypeRequestLookup::TRAVEL)])
            ->get();
    }

    public function getTotalLast7Days(User $user): array
    {
        $results = $this->entity
            ->selectRaw('COUNT(*) AS total, CAST(requests.created_at AS DATE) AS created_at')
            ->join('request_room_view', 'request_room_view.id', '=', 'requests.id')
            ->filterOfficeOrUser($user)
            ->whereDate('requests.created_at', '>=', now()->subDays(7))
            ->groupBy(DB::raw('CAST(requests.created_at AS DATE)'))
            ->get();

        $period = new \DatePeriod(now()->subDays(7), CarbonInterval::day(), now()->addDay());
        return array_map(function ($datePeriod) use ($results) {
            $date = $datePeriod->format('Y-m-d');
            $item = $results->first(function ($values) use ($date) {
                return $values->created_at->toDateString() === $date;
            });
            return ($item) ? (int)$item->total : 0;
        }, iterator_to_array($period));
    }

    public function getTotalRequetsOfMonth(int $officeId): int
    {
        return $this->entity
            ->join('request_room_view', 'request_room_view.id', '=', 'requests.id')
            ->where('requests.created_at', '>=', now()->startOfMonth())
            ->where('requests.created_at', '<=', now()->endOfMonth())
            ->where('request_room_view.office_id', $officeId)
            ->count();
    }

    public function getTotalRequetsOfLastMonth(int $officeId): int
    {
        return $this->entity
            ->join('request_room_view', 'request_room_view.id', '=', 'requests.id')
            ->where('requests.created_at', '>=', now()->subMonth()->startOfMonth())
            ->where('requests.created_at', '<=', now()->subMonth()->endOfMonth())
            ->where('request_room_view.office_id', $officeId)
            ->count();
    }

    public function getTotalRequestRoomOfWeekday(int $userId, int $weekday): int
    {
        return $this->entity
            ->join('lookups AS s', 's.id', '=', 'requests.status_id')
            ->join('lookups AS t', 't.id', '=', 'requests.type_id')
            ->where('user_id', $userId)
            ->whereRaw("DATEPART(WEEKDAY, start_date) = $weekday")
            ->whereDate('start_date', '>=', now())
            ->where('t.code', TypeRequestLookup::code(TypeRequestLookup::ROOM))
            ->whereIn('s.code', [StatusRoomRequestLookup::code(StatusRoomRequestLookup::APPROVED),
                StatusRoomRequestLookup::code(StatusRoomRequestLookup::PROPOSAL),
                StatusRoomRequestLookup::code(StatusRoomRequestLookup::IN_REVIEW)])
            ->count();
    }

    public function getRequestRoomAfterNowInWeekday(int $userId, int $weekday): Collection
    {
        return $this->entity
            ->join('lookups AS s', 's.id', '=', 'requests.status_id')
            ->join('lookups AS t', 't.id', '=', 'requests.type_id')
            ->where('user_id', $userId)
            ->whereRaw("DATEPART(WEEKDAY, start_date) = $weekday")
            ->whereDate('start_date', '>=', now())
            ->where('t.code', TypeRequestLookup::code(TypeRequestLookup::ROOM))
            ->whereNotIn('s.code', [StatusRoomRequestLookup::code(StatusRoomRequestLookup::APPROVED),
                StatusRoomRequestLookup::code(StatusRoomRequestLookup::PROPOSAL),
                StatusRoomRequestLookup::code(StatusRoomRequestLookup::IN_REVIEW)])
            ->get(['requests.*']);
    }

}