<?php

namespace App\Repositories;

use App\Contracts\Repositories\CarRequestScheduleRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\CarRequestSchedule;
use Illuminate\Database\Eloquent\Builder;
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
}