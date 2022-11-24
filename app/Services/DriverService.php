<?php

namespace App\Services;

use App\Contracts\Repositories\DriverRepositoryInterface;
use App\Contracts\Services\DriverServiceInterface;
use App\Core\BaseService;
use App\Helpers\Enum\QueryParam;
use App\Helpers\Validation;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DriverService extends BaseService implements DriverServiceInterface
{
    protected $entityRepository;

    public function __construct(DriverRepositoryInterface $driverRepository)
    {
        $this->entityRepository = $driverRepository;
    }

    public function findAllPaginatedOffice(int $OfficeId, Request $request, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->entityRepository->findAllPaginatedOffice($OfficeId, $filters, $perPage, $sort, $columns);
    }

    public function insertDriverCar(int $carId, int $driverId): void
    {
        $this->entityRepository->sync($driverId, 'cars', ['car_id' => $carId]);
    }
}