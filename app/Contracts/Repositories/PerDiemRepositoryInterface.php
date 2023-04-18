<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\PerDiem;

/**
 * @method PerDiem create(array $data)
 * @method PerDiem update(int $id, array $data)
 * @method PerDiem findById(int $id, array $columns = ['*'])
 */
interface PerDiemRepositoryInterface extends BaseRepositoryInterface
{
    //
}