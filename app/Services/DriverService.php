<?php

namespace App\Services;

use App\Contracts\Repositories\DriverRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\DriverServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\QueryParam;
use App\Helpers\Validation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DriverService extends BaseService implements DriverServiceInterface
{
    protected $entityRepository;
    protected $userRepository;

    public function __construct(DriverRepositoryInterface $driverRepository,
                                UserRepositoryInterface $userRepository)
    {
        $this->entityRepository = $driverRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function findAllPaginatedOffice(int $OfficeId, Request $request, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->userRepository->findAllDriverPaginatedOffice($OfficeId, $filters, $perPage, $sort, $columns);
    }

    public function insertDriverCar(int $carId, int $driverId): void
    {
        $this->userRepository->sync($driverId, 'cars', ['car_id' => $carId]);
    }

    public function findAllByOfficeId(int $officeId): Collection
    {
        return $this->entityRepository->findAllByOfficeId($officeId);
    }

    public function getAvailableDriversPackage(int $officeId, Carbon $date): Collection
    {
        return $this->entityRepository->getAvailableDriversPackage($officeId, $date);
    }

    public function getAvailableDriversRequest(int $officeId, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->userRepository->getAvailableDriversUserRequest($officeId, $startDate, $endDate);
    }

    public function findById(int $id): User
    {
        return $this->userRepository->findByDriverId($id);
    }
}