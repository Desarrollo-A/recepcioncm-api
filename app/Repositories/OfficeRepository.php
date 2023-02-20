<?php

namespace App\Repositories;

use App\Contracts\Repositories\OfficeRepositoryInterface;
use App\Core\BaseRepository;
use App\Exceptions\CustomErrorException;
use App\Models\Enums\Lookups\StatusCarLookup;
use App\Models\Enums\Lookups\StatusUserLookup;
use App\Models\Enums\NameRole;
use App\Models\Office;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class OfficeRepository extends BaseRepository implements OfficeRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|Office
     */
    protected $entity;

    public function __construct(Office $office)
    {
        $this->entity = $office;
    }

    public function findByName(string $name): Office
    {
        return $this->entity
            ->where('name', $name)
            ->firstOr(function () {
                throw new CustomErrorException('No existe el registro de la oficina', Response::HTTP_BAD_REQUEST);
            });
    }

    public function getOfficeByStateWithDriver(int $stateId): Collection
    {
        return $this->entity
            ->where('state_id', $stateId)
            ->whereIn('id', function($query){
                return $query->selectRaw('DISTINCT(office_id)')
                    ->from('users')
                    ->join('lookups', 'lookups.id', '=', 'users.status_id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->where('lookups.code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
                    ->where('roles.name', NameRole::DRIVER);
            })
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function getByStateWithDriverWithoutOffice(Office $office): Collection
    {
        return $this->entity
            ->where('state_id', $office->state_id)
            ->whereIn('id', function($query) {
                return $query->selectRaw('DISTINCT(office_id)')
                    ->from('users')
                    ->join('lookups', 'lookups.id', '=', 'users.status_id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->where('lookups.code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
                    ->where('roles.name', NameRole::DRIVER);
            })
            ->where('id', '!=', $office->id)
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function getOfficeByStateWithDriverAndCar(int $stateId, int $noPeople): Collection
    {
        return $this->entity
            ->where('state_id', $stateId)
            ->whereIn('id', function($query) {
                return $query->selectRaw('DISTINCT(office_id)')
                    ->from('users')
                    ->join('lookups', 'lookups.id', '=', 'users.status_id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->where('lookups.code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
                    ->where('roles.name', NameRole::DRIVER);

            })
            ->whereIn('id', function($query) use ($noPeople) {
                return $query->selectRaw('DISTINCT(office_id)')
                    ->from('cars')
                    ->join('lookups', 'lookups.id', '=', 'cars.status_id')
                    ->where('lookups.code', StatusCarLookup::code(StatusCarLookup::ACTIVE))
                    ->where('people', '>=', $noPeople);
            })
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function getOfficeByStateWithCar(int $stateId, int $noPeople): Collection
    {
        return $this->entity
            ->where('state_id', $stateId)
            ->whereIn('id', function($query) use ($noPeople) {
                return $query->selectRaw('DISTINCT(office_id)')
                    ->from('cars')
                    ->join('lookups', 'lookups.id', '=', 'cars.status_id')
                    ->where('lookups.code', StatusCarLookup::code(StatusCarLookup::ACTIVE))
                    ->where('people', '>=', $noPeople);
            })
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function getOfficeByStateWithDriverAndCarWithoutOffice(Office $office, int $noPeople): Collection
    {
        return $this->entity
            ->where('state_id', $office->state_id)
            ->whereIn('id', function($query) {
                return $query->selectRaw('DISTINCT(office_id)')
                    ->from('users')
                    ->join('lookups', 'lookups.id', '=', 'users.status_id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->where('lookups.code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
                    ->where('roles.name', NameRole::DRIVER);
            })
            ->whereIn('id', function($query) use ($noPeople) {
                return $query->selectRaw('DISTINCT(office_id)')
                    ->from('cars')
                    ->join('lookups', 'lookups.id', '=', 'cars.status_id')
                    ->where('lookups.code', StatusCarLookup::code(StatusCarLookup::ACTIVE))
                    ->where('people', '>=', $noPeople);
            })
            ->where('id', '!=', $office->id)
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function getOfficeByStateWithCarWithoutOffice(Office $office, int $noPeople): Collection
    {
        return $this->entity
            ->where('state_id', $office->state_id)
            ->whereIn('id', function($query) use ($noPeople) {
                return $query->selectRaw('DISTINCT(office_id)')
                    ->from('cars')
                    ->join('lookups', 'lookups.id', '=', 'cars.status_id')
                    ->where('lookups.code', StatusCarLookup::code(StatusCarLookup::ACTIVE))
                    ->where('people', '>=', $noPeople);
            })
            ->where('id', '!=', $office->id)
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function getAll(): Collection
    {
        return $this->entity
            ->with('state')
            ->get();
    }
}