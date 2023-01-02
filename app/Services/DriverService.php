<?php

namespace App\Services;

use App\Contracts\Repositories\CarRepositoryInterface;
use App\Contracts\Repositories\DriverRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\DriverServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\QueryParam;
use App\Helpers\Validation;
use App\Models\Driver;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

class DriverService extends BaseService implements DriverServiceInterface
{
    protected $entityRepository;
    protected $carRepository;
    protected $userRepository;

    public function __construct(DriverRepositoryInterface $driverRepository,
                                CarRepositoryInterface $carRepository,
                                UserRepositoryInterface $userRepository)
    {
        $this->entityRepository = $driverRepository;
        $this->carRepository = $carRepository;
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
        $officeIdCar = $this->carRepository->findById($carId);
        $officeIdDriver = $this->userRepository->findByDriverId($driverId);
        if ($officeIdCar->office_id !== $officeIdDriver->office_id){
            throw new CustomErrorException('La oficina del conductor no coincide con la oficina del automóvil',
                                            Response::HTTP_BAD_REQUEST);
        }
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

    public function findById(int $id): User
    {
        $officeId = auth()->user()->office_id;
        $officeIdDriver = $this->userRepository->findByDriverId($id);
        if($officeId !== $officeIdDriver->office_id){
            throw new AuthorizationException();
        }
        return $officeIdDriver;
    }

    /**
     * @throws CustomErrorException
     */
    public function getAvailableDriversRequest(int $officeId, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->userRepository->getAvailableDriversUserRequest($officeId, $startDate, $endDate);
    }
}