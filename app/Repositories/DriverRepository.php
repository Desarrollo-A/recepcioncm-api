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
            ->driverUser()
            ->where('office_id', $officeId)
            ->get();
    }

    public function getAvailableDriversPackage(int $officeId, Carbon $date): Collection
    {
        return $this->entity
            ->with('cars')
            ->join('car_driver', 'car_driver.driver_id', '=', 'users.id')
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
            ->join('car_driver', 'car_driver.driver_id', '=', 'users.id')
            ->join('lookups', 'lookups.id', '=', 'users.status_id')
            ->where('lookups.code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
            ->where('users.office_id', $officeId)
            ->whereNotIn('users.id', function (QueryBuilder $query) use ($startDate, $endDate) {
                return $query
                    ->select(['driver_id'])
                    ->from('driver_schedules')
                    ->whereDate('start_date', $startDate)
                    ->orWhereDate('start_date', $endDate)
                    ->orWhereDate('end_date', $endDate)
                    ->orWhereDate('end_date', $endDate);
            })
            ->get(['users.*']);
    }
}
