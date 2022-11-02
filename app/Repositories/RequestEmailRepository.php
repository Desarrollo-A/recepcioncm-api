<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestEmailRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\RequestEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class RequestEmailRepository extends BaseRepository implements RequestEmailRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|RequestEmail
     */
    protected $entity;

    public function __construct(RequestEmail $requestEmail)
    {
        $this->entity = $requestEmail;
    }

    public function bulkInsert(array $data): bool
    {
        return $this->entity->insert($data);
    }

    public function findByRequestId(int $requestId, array $columns = ['*']): Collection
    {
        return $this->entity
            ->where('request_id', $requestId)
            ->get($columns);
    }
}