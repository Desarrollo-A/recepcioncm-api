<?php
namespace App\Repositories;

use App\Contracts\Repositories\DriverRepositoryInterface;
use App\Core\BaseRepository;
use App\Helpers\Utils;
use App\Models\DriverParcelDay;
use App\Models\Enums\Lookups\StatusCarLookup;
use App\Models\Enums\Lookups\StatusUserLookup;
use App\Models\Enums\NameRole;
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
            ->with(['cars', 'office', 'role', 'driverParcelDays', 'driverParcelDays.day'])
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

    public function getAvailableDriversPackage(Carbon $date): Collection
    {
        return $this->entity
            ->from('users AS u')
            ->with(['cars', 'office', 'office.state'])
            ->join('roles', 'roles.id', '=', 'u.role_id')
            ->join('lookups', 'lookups.id', '=', 'u.status_id')
            ->join('driver_parcel_days AS dpd', 'dpd.driver_id', '=', 'u.id')
            ->join('lookups AS d', 'dpd.day_id', '=', 'd.id')
            ->where('roles.name', NameRole::DRIVER)
            ->where('lookups.code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
            ->where('d.value', "$date->dayOfWeek")
//            ->whereNotIn('u.id', function (QueryBuilder $query) use ($date) {
//                return $query
//                    ->select(['driver_schedules.driver_id'])
//                    ->from('driver_request_schedules')
//                    ->join('driver_schedules','driver_request_schedules.driver_schedule_id','=','driver_schedules.id')
//                    ->whereDate('driver_schedules.start_date', $date)
//                    ->orWhereDate('driver_schedules.end_date', $date);
//            })
//            ->whereExists(function (QueryBuilder $query) use ($date) {
//                $query
//                    ->selectRaw('COUNT(*)')
//                    ->from('cars')
//                    ->join('lookups', 'lookups.id', '=', 'cars.status_id')
//                    ->where('lookups.code', StatusCarLookup::code(StatusCarLookup::ACTIVE))
//                    ->whereNotIn('cars.id', function (QueryBuilder $query) use ($date) {
//                        return $query
//                            ->selectRaw('DISTINCT(car_id)')
//                            ->from('driver_request_schedules AS drs')
//                            ->join('car_schedules AS cs','drs.car_schedule_id', '=', 'cs.id')
//                            ->whereDate('cs.start_date', $date)
//                            ->orWhereDate('cs.end_date', $date);
//                    })
//                    ->whereNotIn('cars.id', function (QueryBuilder $query) use ($date) {
//                        return $query
//                            ->select(['car_id'])
//                            ->from('car_request_schedules AS crs')
//                            ->join('car_schedules AS cs','crs.car_schedule_id', '=', 'cs.id')
//                            ->whereDate('cs.start_date', $date)
//                            ->orWhereDate('cs.end_date', $date);
//                    })
//                    ->whereNotIn('cars.id', function (QueryBuilder $query) {
//                        return $query
//                            ->select(['car_driver.car_id'])
//                            ->from('users')
//                            ->join('car_driver', 'car_driver.driver_id', '=', 'users.id')
//                            ->whereRaw("users.id <> u.id");
//                    })
//                    ->havingRaw('COUNT(*) > 0');
//            })
            ->get(['u.*']);
    }

    public function getAvailableDriversRequest(int $officeId, Carbon $startDate, Carbon $endDate, int $people): Collection
    {
        $startDateFormat = $startDate->toDateTimeString();
        $endDateFormat = $endDate->toDateTimeString();
        $daysOfWeek = Utils::generateDaysArray($startDate, $endDate);

        return $this->entity
            ->from('users AS u')
            ->join('lookups', 'lookups.id', '=', 'u.status_id')
            ->join('roles', 'roles.id', '=', 'u.role_id')
            ->where('roles.name', NameRole::DRIVER)
            ->where('lookups.code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
            ->where('u.office_id', $officeId)
            ->whereNotIn('u.id', function (QueryBuilder $query) use ($daysOfWeek) {
                return $query
                    ->select(['dpd.driver_id'])
                    ->from('driver_parcel_days AS dpd')
                    ->join('lookups AS d', 'dpd.day_id', '=', 'd.id')
                    ->whereIn('d.value', $daysOfWeek)
                    ->groupBy(['dpd.driver_id']);
            })
            ->whereNotIn('u.id', function (QueryBuilder $query) use ($startDate, $endDate, $startDateFormat, $endDateFormat) {
                return $query
                    ->select(['driver_id'])
                    ->from('driver_request_schedules AS drs')
                    ->join('driver_schedules AS ds', 'drs.driver_schedule_id', '=', 'ds.id')
                    ->where(function (QueryBuilder $query) use ($startDateFormat, $endDateFormat) {
                        $query->where('start_date', '>=', $startDateFormat)
                            ->where('start_date', '<', $endDateFormat);
                    })
                    ->orWhere(function (QueryBuilder $query) use ($startDateFormat, $endDateFormat) {
                        $query->where('end_date', '>', $startDateFormat)
                            ->where('end_date', '<=', $endDateFormat);
                    });
            })
            ->whereNotIn('u.id', function (QueryBuilder $query) use ($startDateFormat, $endDateFormat) {
                return $query
                    ->select(['driver_id'])
                    ->from('driver_package_schedules AS dps')
                    ->join('driver_schedules AS ds', 'dps.driver_schedule_id', '=', 'ds.id')
                    ->whereRaw("(start_date >= '$startDateFormat' AND start_date < '$endDateFormat') OR ".
                        "(end_date > '$startDateFormat' AND end_date <= '$endDateFormat')");
            })
            ->whereExists(function (QueryBuilder $query) use ($officeId, $startDate, $endDate, $startDateFormat, $endDateFormat, $people) {
                $query
                    ->selectRaw('COUNT(*)')
                    ->from('cars')
                    ->join('lookups', 'lookups.id', '=', 'cars.status_id')
                    ->where('lookups.code', StatusCarLookup::code(StatusCarLookup::ACTIVE))
                    ->where('office_id', $officeId)
                    ->where('people', '>=', $people + 1) // Se aumenta 1 por el chofer
                    ->whereNotIn('cars.id', function (QueryBuilder $query) use ($officeId) {
                        return $query
                            ->selectRaw('DISTINCT(car_driver.car_id)')
                            ->from('users')
                            ->join('car_driver', 'car_driver.driver_id', '=', 'users.id')
                            ->whereRaw("users.office_id = $officeId AND users.id <> u.id");
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
                    ->havingRaw('COUNT(*) > 0');
            })
            ->get(['u.*']);
    }

    public function getAvailableDriverProposal(int $officeId, Carbon $startDate, Carbon $endDate): Collection
    {
        $daysOfWeek = Utils::generateDaysArray($startDate, $endDate);

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
            ->whereNotIn('users.id', function (QueryBuilder $query) use ($daysOfWeek) {
                return $query
                    ->select(['dpd.driver_id'])
                    ->from('driver_parcel_days AS dpd')
                    ->join('lookups AS d', 'dpd.day_id', '=', 'd.id')
                    ->whereIn('d.value', $daysOfWeek)
                    ->groupBy(['dpd.driver_id']);
            })
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

    /**
     * @param DriverParcelDay[] $data
     * @throws \Throwable
     */
    public function syncParcelDays(int $id, array $data): void
    {
        $driver = $this->findById($id);
        $driver->driverParcelDays()->delete();
        $driver->driverParcelDays()->saveMany($data);
    }
}
