<?php

namespace App\Services;

use App\Contracts\Repositories\CarDriverRepositoryInterface;
use App\Contracts\Repositories\CarRepositoryInterface;
use App\Contracts\Repositories\DriverPackageScheduleRepositoryInterface;
use App\Contracts\Repositories\DriverRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\RequestCarRepositoryInterface;
use App\Contracts\Services\CarServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\QueryParam;
use App\Helpers\Utils;
use App\Helpers\Validation;
use App\Models\Car;
use App\Models\Dto\CarDTO;
use App\Models\Enums\Lookups\StatusCarLookup;
use App\Models\Enums\TypeLookup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CarService extends BaseService implements CarServiceInterface
{
    protected $entityRepository;
    protected $lookupRepository;
    protected $driverRepository;
    protected $driverPackageScheduleRepository;
    protected $requestCarRepository;
    protected $carDriverRepository;

    public function __construct(
        CarRepositoryInterface $carRepository,
        LookupRepositoryInterface $lookupRepository,
        DriverRepositoryInterface $driverRepository,
        DriverPackageScheduleRepositoryInterface $driverPackageScheduleRepository,
        RequestCarRepositoryInterface $requestCarRepository,
        CarDriverRepositoryInterface $carDriverRepository
    )
    {
        $this->entityRepository = $carRepository;
        $this->lookupRepository = $lookupRepository;
        $this->driverRepository = $driverRepository;
        $this->driverPackageScheduleRepository = $driverPackageScheduleRepository;
        $this->requestCarRepository = $requestCarRepository;
        $this->carDriverRepository = $carDriverRepository;
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

    public function findAllAvailableByDriverId(int $driverId, int $officeId): Collection
    {
        return $this->entityRepository->findAllAvailableByDriverId($driverId, $officeId);
    }

    public function getAvailableCarsInRequestDriver(int $driverId, Carbon $startDate, Carbon $endDate, int $people): Collection
    {
        $driver = $this->driverRepository->findById($driverId);
        return $this->entityRepository->getAvailableCarsInRequestDriver($driver, $startDate, $endDate, $people);
    }

    public function getAvailableCarsInRequestCar(int $officeId, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->entityRepository->getAvailableCarsInRequestCar($officeId, $startDate, $endDate);
    }

    public function getAvailableCarsInRequestPackage(int $driverId, Carbon $startDate): Collection
    {
        $driver = $this->driverRepository->findById($driverId);
        $totalRequestsAssignments = $this->driverPackageScheduleRepository
            ->getTotalAssignmentsByDriverId($driverId, $startDate);
        return $this->entityRepository->getAvailableCarsInRequestPackage($driver, $startDate, $totalRequestsAssignments);
    }

    public function getAvailableCarsProposalRequest(int $requestId, Carbon $dateSelected): \Illuminate\Support\Collection
    {
        $availableCars = [];
        $endDateSelected = $dateSelected;

        $requestCar = $this->requestCarRepository->findByRequestId($requestId);
        $startDateReference = $requestCar->request->start_date;
        $endDateReference = $requestCar->request->end_date;
        $diffInDaysStartDate = $startDateReference->diffInDays($endDateReference);
        $diffInMinutes = $startDateReference->diffInMinutes($endDateReference) / 60;

        if ($diffInDaysStartDate >= 1) {
            $endDateSelected = $dateSelected->addDays($diffInDaysStartDate);
        }

        $availableSchedulesReference = Utils::getAvailableProposalDriverCarSchedule($dateSelected, $endDateSelected, $diffInMinutes);

        $cars = $this->entityRepository
            ->getAvailableRequestCarProposalByOfficeId($requestCar->office_id, $dateSelected, $endDateSelected)
            ->toArray();

        $carBusy = [];
        foreach ($cars as $i => $car) {
            $carBusy[] = $car;
            $availableSchedules = $availableSchedulesReference;
            if ($i+1 < count($cars) && $cars[$i+1]['id'] === $car['id']) {
                continue;
            }

            if (!is_null($car['start_date'])) {
                foreach ($availableSchedules as $index => $time) {
                    foreach ($carBusy as $busy) {
                        if ($this->isScheduleBusy($time, Carbon::make($busy['start_date']), Carbon::make($busy['end_date']))) {
                            unset($availableSchedules[$index]);
                            continue 2;
                        }
                    }
                }
            }

            if (count($availableSchedules) === 0) {
                $carBusy = [];
                continue;
            }

            $availableCar = $car;
            $availableCar['available_schedules'] = $availableSchedules;
            $availableCars[] = $availableCar;
            $carBusy = [];
        }

        return collect($availableCars);
    }

    public function clearRelationWithDriver(int $carId, int $statusId): void
    {
        $status = $this->lookupRepository->findById($statusId);
        if ($status->code === StatusCarLookup::code(StatusCarLookup::DOWN)) {
            $this->carDriverRepository->deleteByCarId($carId);
        }
    }

    private function isScheduleBusy(array $time, Carbon $startTime, Carbon $endTime): bool
    {
        return (($time['start_time']->gte($startTime) && $time['start_time']->lt($endTime)) ||
            ($time['end_time']->gt($startTime) && $time['end_time']->lte($endTime)));
    }
}