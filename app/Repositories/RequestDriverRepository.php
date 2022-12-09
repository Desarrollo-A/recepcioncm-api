<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestDriverRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\RequestDriver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class RequestDriverRepository extends BaseRepository implements RequestDriverRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|RequestDriver
     */
    protected $entity;

    public function __construct(RequestDriver $requestDriver)
    {
        $this->entity = $requestDriver;
    }
}