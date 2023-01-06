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

    public function getAvailableCarsInRequestDriver(int $driverId, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->entity
            ->join('lookups', 'lookups.id', '=', 'cars.status_id')
            ->join('car_driver', 'car_driver.car_id', '=', 'cars.id')
            ->where('lookups.code', StatusCarLookup::code(StatusCarLookup::ACTIVE))
            ->where('car_driver.driver_id', $driverId)
            ->whereNotIn('cars.id', function (QueryBuilder $query) use ($startDate, $endDate) {
                return $query
                    ->select(['car_id'])
                    ->from('car_schedules')
                    ->whereDate('start_date', $startDate)
                    ->orWhereDate('end_date', $startDate)
                    ->whereDate('start_date', $endDate)
                    ->orWhereDate('end_date', $endDate);
            })
            ->get(['cars.*']);
    }

    public function getAvailableCarsInRequestCar(int $officeId, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->entity
            ->join('lookups', 'lookups.id', '=', 'cars.status_id')
            ->where('lookups.code', StatusCarLookup::code(StatusCarLookup::ACTIVE))
            ->where('office_id', $officeId)
            ->whereNotIn('cars.id', function (QueryBuilder $query) use ($startDate, $endDate) {
                return $query
                    ->select(['car_id'])
                    ->from('car_schedules')
                    ->whereDate('start_date', $startDate)
                    ->orWhereDate('end_date', $startDate)
                    ->whereDate('start_date', $endDate)
                    ->orWhereDate('end_date', $endDate);
            })
            ->get(['cars.*']);
    }

    public function getAvailableCarsInRequestPackage(User $user, Carbon $startDate, int $totalAssignments): Collection
    {
        return $this->entity
            ->with('drivers')
            ->join('lookups', 'lookups.id', '=', 'cars.status_id')
            ->where('lookups.code', StatusCarLookup::code(StatusCarLookup::ACTIVE))
            ->where('office_id', $user->office_id)
            ->whereNotIn('cars.id', function (QueryBuilder $query) use ($startDate) {
                return $query
                    ->select(['car_id'])
                    ->from('driver_request_schedules AS drs')
                    ->join('car_schedules AS cs','drs.car_schedule_id', '=', 'cs.id')
                    ->whereDate('cs.start_date', $startDate)
                    ->orWhereDate('cs.end_date', $startDate);
            })
            ->when($totalAssignments === 0, function (Builder $query) use ($user) {
                return $query->whereNotIn('cars.id', function (QueryBuilder $query) use ($user) {
                    return $query
                        ->select(['car_driver.car_id'])
                        ->from('users')
                        ->join('car_driver', 'car_driver.driver_id', '=', 'users.id')
                        ->where('users.office_id', $user->office_id)
                        ->where('users.id', '<>', $user->id);
                });
            })
            ->when($totalAssignments > 0, function (Builder $query) use ($user, $startDate) {
                return $query->whereIn('cars.id', function (QueryBuilder $query) use ($user, $startDate) {
                    return $query
                        ->selectRaw('DISTINCT(cs.car_id)')
                        ->from('driver_package_schedules AS dps')
                        ->join('driver_schedules AS ds', 'dps.driver_schedule_id', '=', 'ds.id')
                        ->join('car_schedules AS cs', 'dps.car_schedule_id', '=', 'cs.id')
                        ->join('packages AS p','dps.package_id','=','p.id')
                        ->join('requests AS r','p.request_id', '=', 'r.id')
                        ->where('ds.driver_id', $user->id)
                        ->whereDate('r.start_date', $startDate);
                });
            })
            ->get(['cars.*']);
    }
}