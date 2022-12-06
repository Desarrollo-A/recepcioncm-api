<?php

namespace App\Repositories;

use App\Contracts\Repositories\CarScheduleRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\CarSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CarScheduleRepository extends BaseRepository implements CarScheduleRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|CarSchedule
     */
    protected $entity;

    public function __construct(CarSchedule $entity)
    {
        $this->entity = $entity;
    }
}