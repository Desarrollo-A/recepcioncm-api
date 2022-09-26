<?php

namespace App\Repositories;

use App\Contracts\Repositories\CancelRequestRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\CancelRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CancelRequestRepository extends BaseRepository implements CancelRequestRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|CancelRequest
     */
    protected $entity;

    public function __construct(CancelRequest $cancelRequest)
    {
        $this->entity = $cancelRequest;
    }
}