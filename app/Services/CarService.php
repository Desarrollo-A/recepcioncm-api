<?php

namespace App\Services;

use App\Contracts\Repositories\CarRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Services\CarServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\QueryParam;
use App\Helpers\Validation;
use App\Models\Car;
use App\Models\Dto\CarDTO;
use App\Models\Enums\Lookups\StatusCarLookup;
use App\Models\Enums\TypeLookup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CarService extends BaseService implements CarServiceInterface
{
    protected $entityRepository;
    protected $lookupRepository;

    public function __construct(CarRepositoryInterface $carRepository,
                                LookupRepositoryInterface $lookupRepository)
    {
        $this->entityRepository = $carRepository;
        $this->lookupRepository = $lookupRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function create(CarDTO $dto): Car
    {
        $dto->status_id = $this->lookupRepository->findByCodeAndType(StatusCarLookup::code(StatusCarLookup::ACTIVE),
            TypeLookup::STATUS_CAR)->id;

        $car = $this->entityRepository->create($dto->toArray(['business_name', 'trademark', 'model',
            'color', 'license_plate', 'serie', 'circulation_card', 'office_id', 'status_id', 'people']));

        return $car->fresh('status', 'office');
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $id, CarDTO $dto): Car
    {
        $car = $this->entityRepository->update($id, $dto->toArray(['business_name', 'trademark', 'model', 'color',
            'license_plate', 'serie', 'circulation_card', 'office_id', 'people']));
        return $car->fresh('status', 'office');
    }

    /**
     * @throws CustomErrorException
     */
    public function findAllPaginatedOffice(Request $request, User $user, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->entityRepository->findAllPaginatedOffice($user, $filters, $perPage, $sort, $columns);
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function changeStatus(int $id, CarDTO $carDTO)
    {
        $this->entityRepository->update($id, $carDTO->toArray(['status_id']));
    }
}