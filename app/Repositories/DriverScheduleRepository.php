<?php

namespace App\Repositories;

use App\Contracts\Repositories\DriverScheduleRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\DriverSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class DriverScheduleRepository extends BaseRepository implements DriverScheduleRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|DriverSchedule
     */
    protected $entity;

    public function __construct(DriverSchedule $entity)
    {
        $this->entity = $entity;
    }
}