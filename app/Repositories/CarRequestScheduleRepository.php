<?php

namespace App\Repositories;

use App\Contracts\Repositories\CarRequestScheduleRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\CarRequestSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CarRequestScheduleRepository extends BaseRepository implements CarRequestScheduleRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|CarRequestSchedule
     */
    protected $entity;

    public function __construct(CarRequestSchedule $carRequestSchedule)
    {
        $this->entity = $carRequestSchedule;
    }

    public function deleteByRequestCarId(int $requestCarId): void
    {
        $this->entity
            ->where('request_car_id', $requestCarId)
            ->delete();
    }

    public function getBusyDaysForProposalCalendar(): Collection
    {
        return $this->entity
            ->selectRaw('DISTINCT CAST(cs.start_date AS DATE) AS start_date, CAST(cs.end_date AS DATE) AS end_date')
            ->from('car_schedules AS cs')
            ->whereDate('cs.start_date', '>=', now())
            ->whereDate('cs.end_date', '>=', now())
            ->get();
    }

    public function bulkDeleteByRequestCarId(array $requestCarIds): bool
    {
        return $this->entity
            ->whereIn('request_car_id', $requestCarIds)
            ->delete();
    }
}