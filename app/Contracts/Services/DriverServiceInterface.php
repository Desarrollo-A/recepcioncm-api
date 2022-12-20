<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @method Driver findById(int $id)
 */

interface DriverServiceInterface extends BaseServiceInterface
{
    public function findAllPaginatedOffice(int $OfficeId, Request $request, array $columns = ['*']): LengthAwarePaginator;

    public function insertDriverCar(int $carId, int $driverId): void;

    public function findAllByOfficeId(int $officeId): Collection;

    public function getAvailableDriversPackage(int $officeId, Carbon $date): Collection;

    public function getAvailableDriversRequest(int $officeId, Carbon $startDate, Carbon $endDate): Collection;
}