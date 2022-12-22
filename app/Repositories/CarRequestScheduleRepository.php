<?php

namespace App\Repositories;

use App\Contracts\Repositories\CarRequestScheduleRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\RequestCar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CarRequestScheduleRepository extends BaseRepository implements CarRequestScheduleRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|RequestCar
     */
    protected $entity;

    public function __construct(RequestCar $requestCar)
    {
        $this->entity = $requestCar;
    }

    public function deleteByRequestCarId(int $requestCarId): void
    {
        $this->entity
            ->where('request_car_id', $requestCarId)
            ->delete();
    }
}