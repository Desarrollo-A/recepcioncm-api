<?php

namespace App\Repositories;

use App\Contracts\Repositories\CarDriverRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\CarDriver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CarDriverRepository extends BaseRepository implements CarDriverRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|CarDriver
     */
    protected $entity;

    public function __construct(CarDriver $entity)
    {
        $this->entity = $entity;
    }

    public function deleteByCarId($carId): bool
    {
        return $this->entity
            ->where('car_id', $carId)
            ->delete();
    }
}