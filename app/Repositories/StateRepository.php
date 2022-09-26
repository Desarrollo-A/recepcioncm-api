<?php

namespace App\Repositories;

use App\Contracts\Repositories\StateRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\State;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class StateRepository extends BaseRepository implements StateRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|State
     */
    protected $entity;

    public function __construct(State $state)
    {
        $this->entity = $state;
    }

    public function getAll(): Collection
    {
        return $this->entity
            ->where('status', true)
            ->get();
    }
}