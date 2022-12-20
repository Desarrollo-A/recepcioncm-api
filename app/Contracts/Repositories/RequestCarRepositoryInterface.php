<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\RequestCar;

/**
 * @method RequestCar create(array $data)
 * @method RequestCar findById(int $id, array $columns = ['*'])
 */
interface RequestCarRepositoryInterface extends BaseRepositoryInterface
{
    public function findByRequestId(int $requestCarId): RequestCar;
}