<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @method User findById(int $id, array $columns = ['*'])
 * @method User create(array $data)
 * @method User update(int $id, array $data)
 */
interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email): User;

    public function findByNoEmployee(string $noEmployee): User;

    public function findByOfficeIdAndRoleRecepcionist(int $officeId): User;

    public function findAllPaginatedWithoutUser(int $userId, array $filters, int $limit, string $sort = null,
                                                array $columns = ['*']): LengthAwarePaginator;

    public function findAllDriverPaginatedOffice(int $OfficeId, array $filters, int $limit, string $sort = null, array $columns = ['*']):
        LengthAwarePaginator;

    public function findAllByOfficeId(int $officeId): Collection;

    public function findByDriverId(int $driverId): User;

    public function getAvailableDriversRequest(int $officeId, Carbon $startDate, Carbon $endDate): Collection;
}