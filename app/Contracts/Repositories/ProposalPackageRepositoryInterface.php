<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\ProposalPackage;

/**
 * @method ProposalPackage create(array $data)
 */
interface ProposalPackageRepositoryInterface extends BaseRepositoryInterface
{
    public function deleteByPackageId(int $packageId): bool;
}