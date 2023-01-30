<?php

namespace App\Repositories;

use App\Contracts\Repositories\DriverRequestScheduleRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\DriverRequestSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class DriverRequestScheduleRepository extends BaseRepository implements DriverRequestScheduleRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|DriverRequestSchedule
     */
    protected $entity;

    public function __construct(DriverRequestSchedule $driverRequestSchedule)
    {
        $this->entity = $driverRequestSchedule;
    }

    public function deleteByRequestDriverId(int $requestDriverId): void
    {
        $this->entity
            ->where('request_driver_id', $requestDriverId)
            ->delete();
    }

    public function getBusyDaysForProposalCalendar(): Collection
    {
        return $this->entity
            ->selectRaw('DISTINCT CAST(ds.start_date AS DATE) AS start_date, CAST(ds.end_date AS DATE) AS end_date')
            ->from('driver_schedules AS ds')
            ->whereDate('ds.start_date', '>=', now())
            ->whereDate('ds.end_date', '>=', now())
            ->get();
    }

    public function bulkDeleteByRequestDriverId(array $requestDriverIds): bool
    {
        return $this->entity
            ->whereIn('request_driver_id', $requestDriverIds)
            ->delete();
    }
}