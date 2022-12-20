<?php
namespace App\Repositories;

use App\Contracts\Repositories\DriverRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Driver;
use App\Models\Enums\Lookups\StatusDriverLookup;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class DriverRepository extends BaseRepository implements DriverRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|Driver
     */
    protected $entity;

    public function __construct(Driver $driver)
    {
        $this->entity = $driver;
    }

    public function findAllPaginatedOffice(int $OfficeId, array $filters, int $limit, string $sort = null, array $columns = ['*']):
        LengthAwarePaginator
    {
        return $this->entity
            ->with('status', 'office')
            ->filter($filters)
            ->where('office_id', $OfficeId)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }

    public function findById(int $id, array $columns = ['*'])
    {
        return $this->entity
            ->with('cars')
            ->findOrFail($id, $columns);
    }

    public function findAllByOfficeId(int $officeId): Collection
    {
        return $this->entity->where('office_id', $officeId)->get();
    }

    public function getAvailableDriversPackage(int $officeId, Carbon $date): Collection
    {
        return $this->entity
            ->with('cars')
            ->join('car_driver', 'car_driver.driver_id', '=', 'drivers.id')
            ->join('lookups', 'lookups.id', '=', 'drivers.status_id')
            ->where('lookups.code', StatusDriverLookup::code(StatusDriverLookup::ACTIVE))
            ->where('drivers.office_id', $officeId)
            ->whereNotIn('drivers.id', function ($query) use ($date) {
                return $query
                    ->select(['driver_schedules.driver_id'])
                    ->from('driver_request_schedules')
                    ->join('driver_schedules','driver_request_schedules.driver_schedule_id','=','driver_schedules.id')
                    ->whereDate('driver_schedules.start_date', $date)
                    ->orWhereDate('driver_schedules.end_date', $date);
            })
            ->get(['drivers.*']);
    }

    public function getAvailableDriversRequest(int $officeId, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->entity
            ->join('car_driver', 'car_driver.driver_id', '=', 'drivers.id')
            ->join('lookups', 'lookups.id', '=', 'drivers.status_id')
            ->where('lookups.code', StatusDriverLookup::code(StatusDriverLookup::ACTIVE))
            ->where('drivers.office_id', $officeId)
            ->whereNotIn('drivers.id', function (QueryBuilder $query) use ($startDate, $endDate) {
                return $query
                    ->select(['driver_id'])
                    ->from('driver_schedules')
                    ->whereDate('start_date', $startDate)
                    ->orWhereDate('start_date', $endDate)
                    ->orWhereDate('end_date', $endDate)
                    ->orWhereDate('end_date', $endDate);
            })
            ->get(['drivers.*']);
    }
}
