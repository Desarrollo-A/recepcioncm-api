<?php

namespace App\Repositories;

use App\Contracts\Repositories\CarRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Car;
use App\Models\Enums\Lookups\StatusCarLookup;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

class CarRepository extends BaseRepository implements CarRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|Car
     */
    protected $entity;

    public function __construct(Car $car)
    {
        $this->entity = $car;
    }

    public function findById(int $id, array $columns = ['*']): Car
    {
        return $this->entity
            ->with('status', 'office')
            ->findOrFail($id, $columns);
    }

    public function findAllPaginatedOffice(User $user, array $filters, int $limit, string $sort = null,
                                           array $columns = ['*']): LengthAwarePaginator
    {
        return $this->entity
            ->with('status')
            ->orWhereHas('status', function ($query) use ($filters) {
                $query->filter($filters);
            })
            ->filter($filters)
            ->filterOffice($user)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }

    public function findAllAvailableByDriverId(int $driverId, int $officeId): Collection
    {
        return $this->entity
            ->join('offices', 'cars.office_id', '=', 'offices.id')
            ->join('lookups', 'cars.status_id', '=', 'lookups.id')
            ->where('lookups.code', StatusCarLookup::code(StatusCarLookup::ACTIVE))
            ->where('cars.office_id', $officeId)
            ->whereNotIn('cars.id', function($query) use ($driverId) {
                return $query->select('car_id')
                    ->from('car_driver')
                    ->where('driver_id', '<>', $driverId);
            })
            ->get('cars.*');
    }
}