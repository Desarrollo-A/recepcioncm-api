<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\Car;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @method Car create(array $data)
 * @method Car findById(int $id, array $columns = ['*'])
 * @method Car update(int $id, array $data)
 */
interface CarRepositoryInterface extends BaseRepositoryInterface
{
    public function findAllPaginatedOffice(User $user, array $filters, int $limit, string $sort = null,
                                           array $columns = ['*']): LengthAwarePaginator;
                                           
    public function findAllAvailableByDriverId(int $driverId, int $officeId): Collection;

    public function getAvailableCarsInRequestDriver(User $driver, Carbon $startDate, Carbon $endDate): Collection;

    public function getAvailableCarsInRequestCar(int $officeId, Carbon $startDate, Carbon $endDate): Collection;

    public function getAvailableCarsInRequestPackage(User $driver, Carbon $startDate, int $totalAssignments): Collection;
}