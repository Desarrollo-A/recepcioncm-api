<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\User;
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

    public function findManagerWhereInNoEmployee(array $codes): User;

    public function findAllDepartmentManagers(): Collection;

    public function findAllUserManagerPermissionPaginated(int $userId, array $filters, int $limit, string $sort = null,
                                                          array $columns = ['*']): LengthAwarePaginator;

    public function findAllUserPermissionPaginated(int $userId, array $filters, int $limit, string $sort = null,
                                                          array $columns = ['*']): LengthAwarePaginator;
}