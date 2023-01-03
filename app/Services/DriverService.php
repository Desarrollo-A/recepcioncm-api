<?php

namespace App\Services;

use App\Contracts\Repositories\CarRepositoryInterface;
use App\Contracts\Repositories\DriverRepositoryInterface;
use App\Contracts\Services\DriverServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\QueryParam;
use App\Helpers\Validation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

class DriverService extends BaseService implements DriverServiceInterface
{
    protected $entityRepository;
    protected $carRepository;

    public function __construct(DriverRepositoryInterface $driverRepository,
                                CarRepositoryInterface $carRepository)
    {
        $this->entityRepository = $driverRepository;
        $this->carRepository = $carRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function findAllPaginatedOffice(int $officeId, Request $request, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->entityRepository->findAllPaginatedOffice($officeId, $filters, $perPage, $sort, $columns);
    }

    /**
     * @throws CustomErrorException
     */
    public function insertDriverCar(int $carId, int $driverId): void
    {
        $car = $this->carRepository->findById($carId);
        $driver = $this->entityRepository->findById($driverId);
        if ($car->office_id !== $driver->office_id){
            throw new CustomErrorException('La oficina del conductor no coincide con la oficina del vehículo',
                                            Response::HTTP_BAD_REQUEST);
        }
        $this->entityRepository->sync($driverId, 'cars', ['car_id' => $carId]);
    }

    public function findAllByOfficeId(int $officeId): Collection
    {
        return $this->entityRepository->findAllByOfficeId($officeId);
    }

    public function getAvailableDriversPackage(int $officeId, Carbon $date): Collection
    {
        return $this->entityRepository->getAvailableDriversPackage($officeId, $date);
    }

    /**
     * @throws AuthorizationException
     */
    public function findById(int $id): User
    {
        $officeId = auth()->user()->office_id;
        $driver = $this->entityRepository->findById($id);
        if($officeId !== $driver->office_id){
            throw new AuthorizationException();
        }
        return $driver;
    }

    public function getAvailableDriversRequest(int $officeId, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->entityRepository->getAvailableDriversRequest($officeId, $startDate, $endDate);
    }
}