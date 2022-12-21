<?php

namespace App\Repositories;

use App\Contracts\Repositories\DriverRequestScheduleRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\DriverRequestSchedule;
use Illuminate\Database\Eloquent\Builder;
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
}