<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Package create(array $data)
 */
interface PackageRepositoryInterface extends BaseRepositoryInterface
{
    public function findByRequestId(int $requestId): Package;

    public function getPackagesByDriverId(int $driverId, Carbon $date): Collection;

    public function findByAuthCode(string $authCodePackage): ?Package;

    public function findAllByDateAndOffice(int $officeId, Carbon $date): Collection;
}