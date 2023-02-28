<?php
namespace App\Repositories;

use App\Contracts\Repositories\DriverRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Enums\Lookups\StatusUserLookup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

class DriverRepository extends BaseRepository implements DriverRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|User
     */
    protected $entity;

    public function __construct(User $driver)
    {
        $this->entity = $driver;
    }

    public function findAllPaginatedOffice(int $officeId, array $filters, int $limit, string $sort = null, array $columns = ['*']):
        LengthAwarePaginator
    {
        return $this->entity
            ->with('status', 'office', 'cars')
            ->driverUser()
            ->filter($filters)
            ->where('office_id', $officeId)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }

    public function findById(int $id, array $columns = ['*'])
    {
        return $this->entity
            ->with(['cars', 'office'])
            ->findOrFail($id, $columns);
    }

    public function findAllByOfficeId(int $officeId): Collection
    {
        return $this->entity
            ->join('lookups', 'lookups.id', '=', 'users.status_id')
            ->driverUser()
            ->where('office_id', $officeId)
            ->where('lookups.code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
            ->get(['users.*']);
    }

    public function getAvailableDriversPackage(int $officeId, Carbon $date): Collection
    {
        return $this->entity
            ->with('cars')
            ->driverUser()
            ->join('lookups', 'lookups.id', '=', 'users.status_id')
            ->where('lookups.code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
            ->where('users.office_id', $officeId)
            ->whereNotIn('users.id', function ($query) use ($date) {
                return $query
                    ->select(['driver_schedules.driver_id'])
                    ->from('driver_request_schedules')
                    ->join('driver_schedules','driver_request_schedules.driver_schedule_id','=','driver_schedules.id')
                    ->whereDate('driver_schedules.start_date', $date)
                    ->orWhereDate('driver_schedules.end_date', $date);
            })
            ->get(['users.*']);
    }

    public function getAvailableDriversRequest(int $officeId, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->entity
            ->driverUser()
            ->join('lookups', 'lookups.id', '=', 'users.status_id')
            ->where('lookups.code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
            ->where('users.office_id', $officeId)
            ->whereNotIn('users.id', function (QueryBuilder $query) use ($startDate, $endDate) {
                return $query
                    ->select(['driver_id'])
                    ->from('driver_request_schedules AS drs')
                    ->join('driver_schedules AS ds', 'drs.driver_schedule_id', '=', 'ds.id')
                    ->where(function (QueryBuilder $query) use ($startDate, $endDate) {
                        $query->where('start_date', '>=', $startDate->toDateTimeString())
                            ->where('start_date', '<', $endDate->toDateTimeString());
                    })
                    ->orWhere(function (QueryBuilder $query) use ($startDate, $endDate) {
                        $query->where('end_date', '>', $startDate->toDateTimeString())
                            ->where('end_date', '<=', $endDate->toDateTimeString());
                    });
            })
            ->whereNotIn('users.id', function (QueryBuilder $query) use ($startDate, $endDate) {
                return $query
                    ->select(['driver_id'])
                    ->from('driver_package_schedules AS dps')
                    ->join('driver_schedules AS ds', 'dps.driver_schedule_id', '=', 'ds.id')
                    ->whereRaw("('{$startDate->toDateTimeString()}' >= start_date AND '{$startDate->toDateTimeString()}' < end_date) OR ".
                        "('{$endDate->toDateTimeString()}' > start_date AND '{$endDate->toDateTimeString()}' <= end_date)");
            })
            ->get(['users.*']);
    }

    public function getAvailableDriverProposal(int $officeId, Carbon $startDate, Carbon $endDate): Collection
    {
        $subQuery = $this->entity
            ->selectRaw("drs.request_driver_id, ds.driver_id, ds.start_date, ds.end_date")
            ->from('driver_request_schedules AS drs')
            ->join('driver_schedules AS ds', 'drs.driver_schedule_id', '=','DS.id')
            ->where(function (Builder $query) use ($startDate, $endDate){
                $query->whereDate('ds.start_date', '>=', $startDate)
                    ->whereDate('ds.start_date', '<=', $endDate);
            })
            ->orWhereDate('ds.end_date', $startDate);

        return $this->entity
            ->driverUser()
            ->select(['users.*','dd.start_date', 'dd.end_date'])
            ->leftJoinSub($subQuery, 'dd', function ($join) {
                $join->on('users.id', '=', 'dd.driver_id');
            })
            ->where('users.office_id', $officeId)
            ->whereNotIn('users.id', function (QueryBuilder $query) use ($startDate, $endDate) {
                return $query
                    ->select('ds.driver_id')
                    ->from('driver_package_schedules AS dps')
                    ->join('driver_schedules AS ds','dps.driver_schedule_id','=','ds.id')
                    ->where(function (QueryBuilder $query) use ($startDate, $endDate){
                        $query->whereDate('ds.start_date', '>=', $startDate)
                            ->whereDate('ds.start_date', '<=', $endDate);
                    })
                    ->orWhereDate('ds.end_date', $startDate);
            })
            ->orderBy('dd.start_date', 'ASC')
            ->orderBy('users.id', 'ASC')
            ->get();
    }
}
