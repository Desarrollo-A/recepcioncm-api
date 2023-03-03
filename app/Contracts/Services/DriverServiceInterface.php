<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @method User findById(int $id)
 */
interface DriverServiceInterface extends BaseServiceInterface
{
    public function findAllPaginatedOffice(int $officeId, Request $request, array $columns = ['*']): LengthAwarePaginator;

    public function insertDriverCar(int $carId, int $driverId): void;

    public function findAllByOfficeId(int $officeId): Collection;

    public function getAvailableDriversPackage(int $officeId, Carbon $date): Collection;

    public function getAvailableDriversRequest(int $requestId, Carbon $startDate, Carbon $endDate): Collection;

    public function getAvailableDriversProposalRequest(int $requestId, Carbon $dateSelected, int $people): \Illuminate\Support\Collection;
}