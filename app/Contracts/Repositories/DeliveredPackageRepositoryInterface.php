<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\DeliveredPackage;

/**
 * @method DeliveredPackage create(array $data)
 * @method DeliveredPackage update(int $id, array $data)
 */
interface DeliveredPackageRepositoryInterface extends BaseRepositoryInterface
{
    //
}