<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestCarViewRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\RequestCarView;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class RequestCarViewRepository extends BaseRepository implements RequestCarViewRepositoryInterface
{
    protected $entity;
    
    public function __construct(RequestCarView $requestCarView)
    {
        $this->entity = $requestCarView;
    }

    public function findAllRequestsCarPaginated(array $filters, int $limit, User $user, string $sort = null,
                                                array $columns = ['*']): LengthAwarePaginator
    {
        return $this->entity
            ->filter($filters)
            ->filterOfficeOrUser($user)
            ->applySort($sort)
            ->paginate($limit);
    }
}