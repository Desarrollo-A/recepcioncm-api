<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\DriverPackageSchedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method DriverPackageSchedule create(array $data)
 */
interface DriverPackageScheduleRepositoryInterface extends BaseRepositoryInterface
{
    public function getScheduleDriverPackage(int $officeId): Collection;

    public function deleteByPackageId(int $packageId): void;

    public function getTotalByStatus(int $driverId, array $statusCodes = []): int;

    public function getTotalAssignmentsByDriverId(int $driverId, Carbon $date): int;
}