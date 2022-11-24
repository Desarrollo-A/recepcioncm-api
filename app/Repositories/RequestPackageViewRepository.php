<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestPackageViewRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\RequestPackageView;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class RequestPackageViewRepository extends BaseRepository implements RequestPackageViewRepositoryInterface
{
    protected $entity;

    public function __construct(RequestPackageView $requestPackageView)
    {
        $this->entity = $requestPackageView;
    }

    public function findAllPackagesPaginated(array $filters, int $limit, User $user, string $sort = null,
                                             array $columns = ['*']): LengthAwarePaginator
    {
        return $this->entity
            ->filter($filters)
            ->filterOfficeOrUser($user)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }
}