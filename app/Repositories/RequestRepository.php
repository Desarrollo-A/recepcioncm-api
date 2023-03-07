<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Core\BaseRepository;
use App\Helpers\Utils;
use App\Models\Enums\Lookups\StatusCarRequestLookup;
use App\Models\Enums\Lookups\StatusDriverRequestLookup;
use App\Models\Enums\Lookups\StatusRoomRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Request;
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
            ->with(['type', 'status', 'score'])
            ->findOrFail($id, $columns);
    }

    public function getRequestRoomScheduleByDate(Carbon $startDate, int $roomId): Collection
    {
        return $this->entity
            ->join('lookups AS s', 's.id', '=', 'requests.status_id')
            ->join('lookups AS t', 't.id', '=', 'requests.type_id')
            ->join('request_room AS rr', 'requests.id', '=', 'rr.request_id')
            ->where('rr.room_id', $roomId)
            ->where('s.code', StatusRoomRequestLookup::code(StatusRoomRequestLookup::APPROVED))
            ->where('t.code', TypeRequestLookup::code(TypeRequestLookup::ROOM))
            ->get(['start_date', 'end_date']);
    }

    public function getProposalRequestRoomScheduleByDate(Carbon $startDate, int $roomId): Collection
    {
        return $this->entity
            ->join('lookups AS s', 's.id', '=', 'requests.status_id')
            ->join('lookups AS t', 't.id', '=', 'requests.type_id')
            ->join('request_room AS rr', 'requests.id', '=', 'rr.request_id')
            ->join('proposal_requests AS pr', 'requests.id', '=', 'pr.request_id')
            ->where('rr.room_id', $roomId)
            ->where('s.code', StatusRoomRequestLookup::code(StatusRoomRequestLookup::PROPOSAL))
            ->where('t.code', TypeRequestLookup::code(TypeRequestLookup::ROOM))
            ->get(['pr.start_date', 'pr.end_date']);
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

    public function getAllApprovedCarDriverRoom(array $columns = ['*']): Collection
    {
        return $this->entity
            ->join('lookups as s', 's.id', '=', 'requests.status_id')
            ->join('lookups as t', 't.id', '=', 'requests.type_id')
            ->whereDate('requests.end_date', '<', now())
            ->whereIn('s.code', [
                StatusCarRequestLookup::code(StatusCarRequestLookup::APPROVED),
                StatusDriverRequestLookup::code(StatusDriverRequestLookup::APPROVED),
                StatusRoomRequestLookup::code(StatusRoomRequestLookup::APPROVED)
            ])
            ->whereIn('t.code', [
                TypeRequestLookup::code(TypeRequestLookup::CAR), TypeRequestLookup::code(TypeRequestLookup::DRIVER),
                TypeRequestLookup::code(TypeRequestLookup::ROOM)
            ])
            ->get($columns);
    }

    public function getExpired(): Collection
    {
        return $this->entity
            ->selectRaw("r.id, s.code AS status_code, t.code AS type_code,".
                "drs.request_driver_id, drs.driver_schedule_id AS drs_driver_schedule_id, drs.car_schedule_id AS drs_car_schedule_id,".
                "crs.request_car_id, crs.car_schedule_id AS crs_car_schedule_id")
            ->from('requests AS r')
            ->join('lookups AS s', 's.id', '=', 'r.status_id')
            ->join('lookups AS t', 't.id', '=', 'r.type_id')
            ->leftJoin('request_drivers AS rd', 'rd.request_id', '=', 'r.id')
            ->leftJoin('driver_request_schedules AS drs', 'drs.request_driver_id', '=', 'rd.id')
            ->leftJoin('request_cars AS rc', 'rc.request_id', '=', 'r.id')
            ->leftJoin('car_request_schedules AS crs', 'crs.request_car_id', '=' ,'rc.id')
            ->whereIn('t.code', Utils::getAllTypesRequest())
            ->whereIn('s.code', Utils::getAllExpiredStatusRequest())
            ->whereDate('r.start_date', '<', now())
            ->get();
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
            ->select(['requests.*', 't.code AS type_code'])
            ->join('lookups AS s', 's.id', '=', 'requests.status_id')
            ->join('lookups AS t', 't.id', '=', 'requests.type_id')
            ->whereDate('start_date', now()->addDay())
            ->whereIn('t.code', Utils::getAllTypesRequest())
            ->whereIn('s.code', Utils::getStatusApprovedRequest())
            ->get();
    }

    public function getTotalLast7Days(int $officeId): array
    {
        $results = $this->entity
            ->selectRaw('COUNT(*) AS total, CAST(requests.created_at AS DATE) AS created_at')
            ->joinAllRecepcionist($officeId)
            ->whereDate('requests.created_at', '>=', now()->subDays(7))
            ->groupBy([DB::raw('CAST(requests.created_at AS DATE)')])
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
            ->joinAllRecepcionist($officeId)
            ->where('requests.created_at', '>=', now()->startOfMonth())
            ->where('requests.created_at', '<=', now()->endOfMonth())
            ->count();
    }

    public function getTotalRequetsOfLastMonth(int $officeId): int
    {
        return $this->entity
            ->joinAllRecepcionist($officeId)
            ->where('requests.created_at', '>=', now()->subMonth()->startOfMonth())
            ->where('requests.created_at', '<=', now()->subMonth()->endOfMonth())
            ->count();
    }

    public function getRequestRoomOfWeekdayByUser(int $userId): Collection
    {
        $union = $this->entity
            ->newModelInstance()
            ->selectRaw('COUNT(*) AS total, DATEPART(WEEKDAY, start_date) AS weekday, r.user_id')
            ->from('requests AS r')
            ->join('lookups AS t', 'r.type_id', '=', 't.id')
            ->join('lookups AS s', 'r.status_id', '=', 's.id')
            ->where('t.code', TypeRequestLookup::code(TypeRequestLookup::ROOM))
            ->where('user_id', $userId)
            ->whereIn('s.code', [
                StatusRoomRequestLookup::code(StatusRoomRequestLookup::APPROVED),
                StatusRoomRequestLookup::code(StatusRoomRequestLookup::IN_REVIEW)
            ])
            ->whereDate('start_date', '>=', now())
            ->groupBy([DB::raw('DATEPART(WEEKDAY, start_date)'), 'r.user_id']);

        return $this->entity
            ->selectRaw('COUNT(*) AS total, DATEPART(WEEKDAY, pr.start_date) AS weekday, r.user_id')
            ->from('requests AS r')
            ->join('proposal_requests AS pr', 'pr.request_id', '=', 'r.id')
            ->join('lookups AS t', 'r.type_id', '=', 't.id')
            ->join('lookups AS s', 'r.status_id', '=', 's.id')
            ->where('t.code', TypeRequestLookup::code(TypeRequestLookup::ROOM))
            ->where('user_id', $userId)
            ->where('s.code', StatusRoomRequestLookup::code(StatusRoomRequestLookup::PROPOSAL))
            ->whereDate('pr.start_date', '>=', now())
            ->union($union)
            ->groupBy([DB::raw('DATEPART(WEEKDAY, pr.start_date)'), 'r.user_id'])
            ->get();
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
            ->where('s.code', StatusRoomRequestLookup::code(StatusRoomRequestLookup::NEW))
            ->get(['requests.*']);
    }

    public function getTotalApplicantByStatus(int $userId, array $statusCodes = []): int
    {
        return $this->entity
            ->join('lookups AS s', 's.id', '=', 'requests.status_id')
            ->join('lookups AS t', 't.id', '=', 'requests.type_id')
            ->where('user_id', $userId)
            ->whereIn('t.code', Utils::getAllTypesRequest())
            ->when(!empty($statusCodes), function (Builder $query) use ($statusCodes) {
                return $query->whereIn('s.code', $statusCodes);
            })
            ->count();
    }

    public function getTotalRecepcionistByStatus(int $officeId, array $statusCodes = []): int
    {
        return $this->entity
            ->joinAllRecepcionist($officeId)
            ->whereIn('t.code', Utils::getAllTypesRequest())
            ->when(!empty($statusCodes), function (Builder $query) use ($statusCodes) {
                return $query->whereIn('s.code', $statusCodes);
            })
            ->count();
    }

    public function getAllApprovedRecepcionistWithStartDateCondition(int $officeId, string $startDateOperator = '='): Collection
    {
        return $this->entity
            ->with(['type',

                'requestRoom', 'requestRoom.room', 'requestRoom.room.office',

                'package', 'package.driverPackageSchedule', 'package.driverPackageSchedule.carSchedule',
                'package.driverPackageSchedule.driverSchedule', 'package.driverPackageSchedule.carSchedule.car',
                'package.driverPackageSchedule.driverSchedule.driver',

                'requestDriver', 'requestDriver.driverRequestSchedule', 'requestDriver.driverRequestSchedule.carSchedule',
                'requestDriver.driverRequestSchedule.driverSchedule', 'requestDriver.driverRequestSchedule.carSchedule.car',
                'requestDriver.driverRequestSchedule.driverSchedule.driver',

                'requestCar', 'requestCar.carRequestSchedule', 'requestCar.carRequestSchedule.carSchedule',
                'requestCar.carRequestSchedule.carSchedule.car'])
            ->joinAllRecepcionist($officeId)
            ->whereIn('t.code', Utils::getAllTypesRequest())
            ->whereIn('s.code', Utils::getStatusApprovedRequest())
            ->whereDate('requests.start_date', $startDateOperator, now())
            ->orderBy('requests.start_date', 'ASC')
            ->get(['requests.*']);
    }

    public function getAllApprovedApplicantWithStartDateCondition(int $userId, string $startDateOperator = '='): Collection
    {
        return $this->entity
            ->with(['type',

                'requestRoom', 'requestRoom.room', 'requestRoom.room.office',

                'package', 'package.driverPackageSchedule', 'package.driverPackageSchedule.carSchedule',
                'package.driverPackageSchedule.driverSchedule', 'package.driverPackageSchedule.carSchedule.car',
                'package.driverPackageSchedule.driverSchedule.driver',

                'requestDriver', 'requestDriver.driverRequestSchedule', 'requestDriver.driverRequestSchedule.carSchedule',
                'requestDriver.driverRequestSchedule.driverSchedule', 'requestDriver.driverRequestSchedule.carSchedule.car',
                'requestDriver.driverRequestSchedule.driverSchedule.driver',

                'requestCar', 'requestCar.office', 'requestCar.carRequestSchedule',
                'requestCar.carRequestSchedule.carSchedule', 'requestCar.carRequestSchedule.carSchedule.car'])
            ->join('lookups AS s', 's.id', '=', 'requests.status_id')
            ->join('lookups AS t', 't.id', '=', 'requests.type_id')
            ->where('user_id', $userId)
            ->whereIn('t.code', Utils::getAllTypesRequest())
            ->whereIn('s.code', Utils::getStatusApprovedRequest())
            ->whereDate('requests.start_date', $startDateOperator, now())
            ->orderBy('requests.start_date', 'ASC')
            ->get(['requests.*']);
    }
}