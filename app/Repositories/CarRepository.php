<?php

namespace App\Repositories;

use App\Contracts\Repositories\CarRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Car;
use App\Models\Enums\Lookups\StatusCarLookup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

class CarRepository extends BaseRepository implements CarRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|Car
     */
    protected $entity;

    public function __construct(Car $car)
    {
        $this->entity = $car;
    }

    public function findById(int $id, array $columns = ['*']): Car
    {
        return $this->entity
            ->with('status', 'office')
            ->findOrFail($id, $columns);
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

    public function findAllAvailableByDriverId(int $driverId, int $officeId): Collection
    {
        return $this->entity
            ->join('offices', 'cars.office_id', '=', 'offices.id')
            ->join('lookups', 'cars.status_id', '=', 'lookups.id')
            ->where('lookups.code', StatusCarLookup::code(StatusCarLookup::ACTIVE))
            ->where('cars.office_id', $officeId)
            ->whereNotIn('cars.id', function($query) use ($driverId) {
                return $query->select('car_id')
                    ->from('car_driver')
                    ->where('driver_id', '<>', $driverId);
            })
            ->get('cars.*');
    }

    public function getAvailableCarsInRequestDriver(User $driver, Carbon $startDate, Carbon $endDate, int $people): Collection
    {
        $startDateFormat = $startDate->toDateTimeString();
        $endDateFormat = $endDate->toDateTimeString();
        return $this->entity
            ->with('drivers')
            ->join('lookups', 'lookups.id', '=', 'cars.status_id')
            ->where('lookups.code', StatusCarLookup::code(StatusCarLookup::ACTIVE))
            ->where('office_id', $driver->office_id)
            ->where('people', '>=', $people + 1) // Se aumenta 1 por el chofer
            ->whereNotIn('cars.id', function (QueryBuilder $query) use ($driver) {
                return $query
                    ->selectRaw('DISTINCT(car_driver.car_id)')
                    ->from('users')
                    ->join('car_driver', 'car_driver.driver_id', '=', 'users.id')
                    ->where('users.office_id', $driver->office_id)
                    ->where('users.id', '<>', $driver->id);
            })
            ->whereNotIn('cars.id', function (QueryBuilder $query) use ($startDate, $endDate) {
                return $query
                    ->select(['car_id'])
                    ->from('driver_package_schedules AS dps')
                    ->join('car_schedules AS cs', 'dps.car_schedule_id', '=', 'cs.id')
                    ->where(function (QueryBuilder $query) use ($startDate, $endDate) {
                        $query->whereDate('start_date', $startDate)
                            ->orWhereDate('start_date', $endDate);
                    })
                    ->orWhere(function (QueryBuilder $query) use ($startDate, $endDate) {
                        $query->whereDate('end_date', $startDate)
                            ->orWhereDate('end_date', $endDate);
                    });
            })
            ->whereNotIn('cars.id', function (QueryBuilder $query) use ($startDateFormat, $endDateFormat) {
                return $query
                    ->select(['car_id'])
                    ->from('driver_request_schedules AS drs')
                    ->join('car_schedules AS cs', 'drs.car_schedule_id', '=', 'cs.id')
                    ->whereRaw("(start_date >= '$startDateFormat' AND start_date < '$endDateFormat') OR ".
                        "(end_date > '$startDateFormat' AND end_date <= '$endDateFormat')");
            })
            ->whereNotIn('cars.id', function (QueryBuilder $query) use ($startDateFormat, $endDateFormat) {
                return $query
                    ->select(['car_id'])
                    ->from('car_request_schedules AS crs')
                    ->join('car_schedules AS cs', 'crs.car_schedule_id', '=', 'cs.id')
                    ->whereRaw("(start_date >= '$startDateFormat' AND start_date < '$endDateFormat') OR ".
                        "(end_date > '$startDateFormat' AND end_date <= '$endDateFormat')");
            })
            ->get(['cars.*']);
    }

    public function getAvailableCarsInRequestCar(int $officeId, Carbon $startDate, Carbon $endDate): Collection
    {
        $startDateFormat = $startDate->toDateTimeString();
        $endDateFormat = $endDate->toDateTimeString();

        return $this->entity
            ->join('lookups', 'lookups.id', '=', 'cars.status_id')
            ->where('lookups.code', StatusCarLookup::code(StatusCarLookup::ACTIVE))
            ->where('office_id', $officeId)
            ->whereNotIn('cars.id', function (QueryBuilder $query) use ($startDate, $endDate) {
                return $query
                    ->select(['car_id'])
                    ->from('driver_package_schedules AS dps')
                    ->join('car_schedules AS cs', 'dps.car_schedule_id', '=', 'cs.id')
                    ->where(function (QueryBuilder $query) use ($startDate, $endDate) {
                        $query->whereDate('start_date', $startDate)
                            ->orWhereDate('start_date', $endDate);
                    })
                    ->orWhere(function (QueryBuilder $query) use ($startDate, $endDate) {
                        $query->whereDate('end_date', $startDate)
                            ->orWhereDate('end_date', $endDate);
                    });
            })
            ->whereNotIn('cars.id', function (QueryBuilder $query) use ($startDateFormat, $endDateFormat) {
                return $query
                    ->select(['car_id'])
                    ->from('driver_request_schedules AS drs')
                    ->join('car_schedules AS cs', 'drs.car_schedule_id', '=', 'cs.id')
                    ->whereRaw("(start_date >= '$startDateFormat' AND start_date < '$endDateFormat') OR ".
                        "(end_date > '$startDateFormat' AND end_date <= '$endDateFormat')");
            })
            ->whereNotIn('cars.id', function (QueryBuilder $query) use ($startDateFormat, $endDateFormat) {
                return $query
                    ->select(['car_id'])
                    ->from('car_request_schedules AS crs')
                    ->join('car_schedules AS cs', 'crs.car_schedule_id', '=', 'cs.id')
                    ->whereRaw("(start_date >= '$startDateFormat' AND start_date < '$endDateFormat') OR ".
                        "(end_date > '$startDateFormat' AND end_date <= '$endDateFormat')");
            })
            ->get(['cars.*']);
    }

    public function getAvailableCarsInRequestPackage(User $driver, Carbon $startDate, int $totalAssignments): Collection
    {
        return $this->entity
            ->with('drivers')
            ->join('lookups', 'lookups.id', '=', 'cars.status_id')
            ->where('lookups.code', StatusCarLookup::code(StatusCarLookup::ACTIVE))
            ->where('office_id', $driver->office_id)
            ->whereNotIn('cars.id', function (QueryBuilder $query) use ($startDate) {
                return $query
                    ->selectRaw('DISTINCT(car_id)')
                    ->from('driver_request_schedules AS drs')
                    ->join('car_schedules AS cs','drs.car_schedule_id', '=', 'cs.id')
                    ->whereDate('cs.start_date', $startDate)
                    ->orWhereDate('cs.end_date', $startDate);
            })
            ->whereNotIn('cars.id', function (QueryBuilder $query) use ($startDate) {
                return $query
                    ->select(['car_id'])
                    ->from('car_request_schedules AS crs')
                    ->join('car_schedules AS cs','crs.car_schedule_id', '=', 'cs.id')
                    ->whereDate('cs.start_date', $startDate)
                    ->orWhereDate('cs.end_date', $startDate);
            })
            ->when($totalAssignments === 0, function (Builder $query) use ($driver) {
                return $query->whereNotIn('cars.id', function (QueryBuilder $query) use ($driver) {
                    return $query
                        ->select(['car_driver.car_id'])
                        ->from('users')
                        ->join('car_driver', 'car_driver.driver_id', '=', 'users.id')
                        ->where('users.office_id', $driver->office_id)
                        ->where('users.id', '<>', $driver->id);
                });
            })
            ->when($totalAssignments > 0, function (Builder $query) use ($driver, $startDate) {
                return $query->whereIn('cars.id', function (QueryBuilder $query) use ($driver, $startDate) {
                    return $query
                        ->selectRaw('DISTINCT(cs.car_id)')
                        ->from('driver_package_schedules AS dps')
                        ->join('driver_schedules AS ds', 'dps.driver_schedule_id', '=', 'ds.id')
                        ->join('car_schedules AS cs', 'dps.car_schedule_id', '=', 'cs.id')
                        ->join('packages AS p','dps.package_id','=','p.id')
                        ->join('requests AS r','p.request_id', '=', 'r.id')
                        ->where('ds.driver_id', $driver->id)
                        ->whereDate('r.start_date', $startDate);
                });
            })
            ->get(['cars.*']);
    }

    public function getAvailableRequestDriverProposalByDriverId(int $driverId, int $officeId, int $people,
                                                                Carbon $startDate, Carbon $endDate): Collection
    {
        $subQuery = $this->entity
            ->selectRaw("cs.id AS car_sche_id, cs.car_id, cs.start_date, cs.end_date")
            ->from('car_schedules AS cs')
            ->leftJoin('car_request_schedules AS crs', 'cs.id', '=', 'crs.car_schedule_id')
            ->leftJoin('driver_request_schedules AS drs','cs.id', '=', 'drs.car_schedule_id')
            ->where(function (Builder $query) use ($startDate, $endDate){
                $query->whereDate('cs.start_date', '>=', $startDate)
                    ->whereDate('cs.start_date', '<=', $endDate);
            })
            ->orWhereDate('cs.end_date', $startDate);

        return $this->entity
            ->select(['cars.*', 'cd.driver_id', 'dc.start_date', 'dc.end_date'])
            ->leftJoin('car_driver AS cd', 'cars.id', '=', 'cd.car_id')
            ->leftJoinSub($subQuery, 'dc', function ($join) {
                $join->on('cars.id', '=', 'dc.car_id');
            })
            ->where(function (Builder $query) use ($driverId) {
                $query->whereNull('cd.driver_id')
                    ->orWhere('cd.driver_id', $driverId);
            })
            ->where('cars.office_id', $officeId)
            ->where('people', '>=', $people + 1) // Se aumenta 1 por el chofer
            ->where(function (Builder $query) use ($officeId) {
                $query->whereNotIn('dc.car_sche_id', function (QueryBuilder $query) {
                    return $query
                        ->select('car_schedule_id')
                        ->from('driver_package_schedules');
                })->orWhereNull('dc.car_sche_id');
            })
            ->get();
    }

    public function getAvailableRequestCarProposalByOfficeId(int $officeId, Carbon $startDate, Carbon $endDate): Collection
    {
        $subQuery = $this->entity
            ->selectRaw("cs.id AS car_sche_id, cs.car_id, cs.start_date, cs.end_date")
            ->from('car_schedules AS cs')
            ->leftJoin('car_request_schedules AS crs', 'cs.id', '=', 'crs.car_schedule_id')
            ->leftJoin('driver_request_schedules AS drs','cs.id', '=', 'drs.car_schedule_id')
            ->where(function (Builder $query) use ($startDate, $endDate){
                $query->whereDate('cs.start_date', '>=', $startDate)
                    ->whereDate('cs.start_date', '<=', $endDate);
            })
            ->orWhereDate('cs.end_date', $startDate);

        return $this->entity
            ->select(['cars.*', 'cd.driver_id', 'dc.start_date', 'dc.end_date'])
            ->leftJoin('car_driver AS cd', 'cars.id', '=', 'cd.car_id')
            ->leftJoinSub($subQuery, 'dc', function ($join) {
                $join->on('cars.id', '=', 'dc.car_id');
            })
            ->where('cars.office_id', $officeId)
            ->where(function (Builder $query) use ($officeId) {
                $query->whereNotIn('dc.car_sche_id', function (QueryBuilder $query) {
                    return $query
                        ->select('car_schedule_id')
                        ->from('driver_package_schedules');
                })->orWhereNull('dc.car_sche_id');
            })
            ->get();
    }
}