<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestDriverViewRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\RequestDriverView;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

class RequestDriverViewRepository extends BaseRepository implements RequestDriverViewRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|RequestDriverView
     */
    protected $entity;

    public function __construct(RequestDriverView $requestDriverView)
    {
        $this->entity = $requestDriverView;
    }

    public function findAllDriversPaginated(array $filters, int $limit, User $user, string $sort = null, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->entity
            ->filter($filters)
            ->filterOfficeOrUser($user)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }
}