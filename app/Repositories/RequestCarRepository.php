<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestCarRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\RequestCar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class RequestCarRepository extends BaseRepository implements RequestCarRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|RequestCar
     */
    protected $entity;

    public function __construct(RequestCar $requestCar)
    {
        $this->entity = $requestCar;
    }
}