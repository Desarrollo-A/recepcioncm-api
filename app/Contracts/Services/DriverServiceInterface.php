<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface DriverServiceInterface extends BaseServiceInterface
{
    public function findAllPaginatedOffice(int $OfficeId, Request $request, array $columns = ['*']): LengthAwarePaginator;

    public function insertDriverCar(int $carId, int $driverId): void;

    public function findAllByOfficeId(int $officeId): Collection;
}