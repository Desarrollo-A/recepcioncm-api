<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Core\BaseRepository;
use App\Exceptions\CustomErrorException;
use App\Models\Enums\Lookups\StatusDriverLookup;
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

    public function findByOfficeIdAndRoleRecepcionist(int $officeId): User
    {
        return $this->entity
            ->whereHas('role', function (Builder $query) {
                $query->where('name', NameRole::RECEPCIONIST);
            })
            ->whereHas('status', function(Builder $query){
                $query->where('code', StatusUserLookup::code(StatusUserLookup::ACTIVE));
            })
            ->where('office_id', $officeId)
            ->firstOr(function () {
                throw new CustomErrorException('No hay una recepcionista asignada en esta oficina.',
                    Response::HTTP_BAD_REQUEST);
            });
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

    public function findAllDriverPaginatedOffice(int $OfficeId, array $filters, int $limit, string $sort = null, array $columns = ['*']):
        LengthAwarePaginator
    {
        return $this->entity
            ->with('status', 'office')
            ->filter($filters)
            ->driverUser()
            ->where('office_id', $OfficeId)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }

    public function findAllByOfficeId(int $officeId): Collection
    {
        return $this->entity
            ->driverUser()
            ->where('office_id', $officeId)
            ->get();
    }

    public function findByDriverId(int $driverId): User
    {
        return $this->entity
            ->with(['status', 'office', 'cars'])
            ->where('id', $driverId)
            ->firstOrFail();
    }

    public function getAvailableDriversRequest(int $officeId, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->entity
            ->driverUser()
            ->join('car_driver', 'car_driver.driver_id', '=', 'users.id')
            ->join('lookups', 'lookups.id', '=', 'users.status_id')
            ->where('lookups.code', StatusDriverLookup::code(StatusDriverLookup::ACTIVE))
            ->where('users.office_id', $officeId)
            ->whereNotIn('users.id', function (QueryBuilder $query) use ($startDate, $endDate) {
                return $query
                    ->select(['driver_id'])
                    ->from('driver_schedules')
                    ->whereDate('start_date', $startDate)
                    ->orWhereDate('start_date', $endDate)
                    ->orWhereDate('end_date', $endDate)
                    ->orWhereDate('end_date', $endDate);
            })
            ->get(['users.*']);
    }
}