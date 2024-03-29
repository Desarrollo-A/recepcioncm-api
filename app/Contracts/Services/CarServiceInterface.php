<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Car;
use App\Models\Dto\CarDTO;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface CarServiceInterface extends BaseServiceInterface
{
    public function create(CarDTO $dto): Car;

    public function update(int $id, CarDTO $dto): Car;

    public function findAllPaginatedOffice(Request $request, User $user, array $columns = ['*']): LengthAwarePaginator;

    /**
     * @return void
     */
    public function changeStatus(int $id, CarDTO $carDTO);

    public function findAllAvailableByDriverId(int $driverId, int $officeId): Collection;

    public function getAvailableCarsInRequestDriver(int $driverId, Carbon $startDate, Carbon $endDate, int $people): Collection;

    public function getAvailableCarsInRequestCar(int $officeId, Carbon $startDate, Carbon $endDate): Collection;

    public function getAvailableCarsInRequestPackage(int $driverId, Carbon $startDate): Collection;

    public function getAvailableCarsProposalRequest(int $requestId, Carbon $dateSelected): \Illuminate\Support\Collection;

    public function clearRelationWithDriver(int $carId, int $statusId): void;
}