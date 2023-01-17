<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestPackageViewRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Enums\Lookups\StatusPackageRequestLookup;
use App\Models\RequestPackageView;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

class RequestPackageViewRepository extends BaseRepository implements RequestPackageViewRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|RequestPackageView
     */
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

    public function findAllByDriverIdPaginated(array $filters, int $limit, User $user, string $sort = null,
                                                       array $columns = ['*']): LengthAwarePaginator
    {
        return $this->entity
            ->filter($filters)
            ->where('driver_id', $user->id)
            ->whereIn('status_code', [
                StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED),
                StatusPackageRequestLookup::code(StatusPackageRequestLookup::ROAD),
            ])
            ->applySort($sort)
            ->paginate($limit, $columns);
    }
}