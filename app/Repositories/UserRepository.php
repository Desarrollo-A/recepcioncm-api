<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Core\BaseRepository;
use App\Exceptions\CustomErrorException;
use App\Models\Enums\Lookups\StatusUserLookup;
use App\Models\Enums\NameRole;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|User
     */
    protected $entity;

    public function __construct(User $user)
    {
        $this->entity = $user;
    }

    public function findById(int $id, array $columns = ['*']): User
    {
        return $this->entity
            ->with(['status', 'role', 'office'])
            ->findOrFail($id, $columns);
    }

    public function findByEmail(string $email): User
    {
        return $this->entity
            ->with('status')
            ->where('email', $email)
            ->firstOrFail();
    }

    public function findByNoEmployee(string $noEmployee): User
    {
        return $this->entity
            ->with(['status', 'role'])
            ->where('no_employee', $noEmployee)
            ->firstOrFail();
    }

    public function findByOfficeIdAndRoleRecepcionist(int $officeId): Collection
    {
        return $this->entity
            ->whereHas('role', function (Builder $query) {
                $query->where('name', NameRole::RECEPCIONIST);
            })
            ->whereHas('status', function(Builder $query){
                $query->where('code', StatusUserLookup::code(StatusUserLookup::ACTIVE));
            })
            ->where('office_id', $officeId)
            ->get();
    }

    public function findAllPaginatedWithoutUser(int $userId, array $filters, int $limit, string $sort = null,
                                                array $columns = ['*']): LengthAwarePaginator
    {
        return $this->entity
            ->with(['role', 'status'])
            ->orWhereHas('role', function (Builder $query) use ($filters) {
                $query->filter($filters);
            })
            ->orWhereHas('status', function (Builder $query) use ($filters) {
                $query->filter($filters);
            })
            ->filter($filters)
            ->withoutUser($userId)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }

    public function findManagerWhereInNoEmployee(array $codes): User
    {
        return $this->entity
            ->whereHas('role', function (Builder $query) {
                $query->where('name', NameRole::DEPARTMENT_MANAGER);
            })
            ->whereHas('status', function(Builder $query) {
                $query->where('code', StatusUserLookup::code(StatusUserLookup::ACTIVE));
            })
            ->whereIn('no_employee', $codes)
            ->firstOr(function () {
                throw new CustomErrorException('No existe el director.',
                    Response::HTTP_BAD_REQUEST);
            });
    }

    public function findAllDepartmentManagers(): Collection
    {
        return $this->entity
            ->whereHas('role', function (Builder $query) {
                $query->where('name', NameRole::DEPARTMENT_MANAGER);
            })
            ->whereHas('status', function(Builder $query) {
                $query->where('code', StatusUserLookup::code(StatusUserLookup::ACTIVE));
            })
            ->get();
    }

    public function findAllUserManagerPermissionPaginated(int $userId, array $filters, int $limit, string $sort = null,
                                                          array $columns = ['*']): LengthAwarePaginator
    {
        return $this->entity
            ->whereHas('role', function (Builder $query) {
                $query->where('name', NameRole::RECEPCIONIST);
            })
            ->whereHas('status', function(Builder $query) {
                $query->where('code', StatusUserLookup::code(StatusUserLookup::ACTIVE));
            })
            ->where('department_manager_id', $userId)
            ->filter($filters)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }

    public function findAllUserPermissionPaginated(int $userId, array $filters, int $limit, string $sort = null, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->entity
            ->whereHas('status', function(Builder $query) {
                $query->where('code', StatusUserLookup::code(StatusUserLookup::ACTIVE));
            })
            ->withoutUser($userId)
            ->filter($filters)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }
}