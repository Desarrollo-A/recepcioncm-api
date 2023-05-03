<?php

namespace App\Repositories;

use App\Contracts\Repositories\MovementRequestRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\MovementRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class MovementRequestRepository extends BaseRepository implements MovementRequestRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|MovementRequest
     */
    protected $entity;

    public function __construct(MovementRequest $entity)
    {
        $this->entity = $entity;
    }
}