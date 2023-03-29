<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\DetailExternalParcel;

/**
 * @method DetailExternalParcel create(array $data)
 */
interface DetailExternalParcelRepositoryInterface extends BaseRepositoryInterface
{
    public function deleteByPackageId(int $packageId): bool;
}