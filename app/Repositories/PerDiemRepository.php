<?php

namespace App\Repositories;

use App\Contracts\Repositories\PerDiemRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\PerDiem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class PerDiemRepository extends BaseRepository implements PerDiemRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|PerDiem
     */
    protected $entity;

    public function __construct(PerDiem $perDiem)
    {
        $this->entity = $perDiem;
    }
}