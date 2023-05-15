<?php

namespace App\Services;

use App\Contracts\Repositories\CarDriverRepositoryInterface;
use App\Contracts\Repositories\CarRepositoryInterface;
use App\Contracts\Repositories\DriverRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\RequestDriverRepositoryInterface;
use App\Contracts\Services\DriverServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\QueryParam;
use App\Helpers\Utils;
use App\Helpers\Validation;
use App\Models\DriverParcelDay;
use App\Models\Dto\UserDTO;
use App\Models\Enums\Lookups\StatusUserLookup;
use App\Models\Enums\NameRole;
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
    protected $requestDriverRepository;
    protected $lookupRepository;
    protected $carDriverRepository;

    public function __construct(
        DriverRepositoryInterface $driverRepository,
        CarRepositoryInterface $carRepository,
        RequestDriverRepositoryInterface $requestDriverRepository,
        LookupRepositoryInterface $lookupRepository,
        CarDriverRepositoryInterface $carDriverRepository
    )
    {
        $this->entityRepository = $driverRepository;
        $this->carRepository = $carRepository;
        $this->requestDriverRepository = $requestDriverRepository;
        $this->lookupRepository = $lookupRepository;
        $this->carDriverRepository = $carDriverRepository;
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

    public function getAvailableDriversRequest(int $requestId, Carbon $startDate, Carbon $endDate): Collection
    {
        $requestDriver = $this->requestDriverRepository->findByRequestId($requestId);
        return $this->entityRepository->getAvailableDriversRequest(
            $requestDriver->office_id, $startDate, $endDate, $requestDriver->request->people
        );
    }

    public function getAvailableDriversProposalRequest(int $requestId, Carbon $dateSelected, int $people): \Illuminate\Support\Collection
    {
        $availableDrivers = [];
        $endDateSelected = $dateSelected;

        $requestDriver = $this->requestDriverRepository->findByRequestId($requestId);
        $startDateReference = $requestDriver->request->start_date;
        $endDateReference = $requestDriver->request->end_date;
        $diffInDaysStartDate = $startDateReference->diffInDays($endDateReference);
        $diffInMinutes = $startDateReference->diffInMinutes($endDateReference) / 60;

        if ($diffInDaysStartDate >= 1) {
            $endDateSelected = $dateSelected->addDays($diffInDaysStartDate);
        }

        $availableSchedulesReference = Utils::getAvailableProposalDriverCarSchedule($dateSelected, $endDateSelected, $diffInMinutes);

        $drivers = $this->entityRepository
            ->getAvailableDriverProposal($requestDriver->office_id, $dateSelected, $endDateSelected)->toArray();
        $driverBusy = [];

        foreach ($drivers as $i => $driver) {
            $driverBusy[] = $driver;
            $availableSchedules = $availableSchedulesReference;
            if ($i+1 < count($drivers) && $drivers[$i+1]['id'] === $driver['id']) {
                continue;
            }

            if (!is_null($driver['start_date'])) {
                foreach ($availableSchedules as $index => $time) {
                    foreach ($driverBusy as $busy) {
                        if ($this->isScheduleBusy($time, Carbon::make($busy['start_date']), Carbon::make($busy['end_date']))) {
                            unset($availableSchedules[$index]);
                            continue 2;
                        }
                    }
                }
            }

            if (count($availableSchedules) === 0) {
                $driverBusy = [];
                continue;
            }

            $cars = $this->carRepository->getAvailableRequestDriverProposalByDriverId($driver['id'], $driver['office_id'],
                $people, $dateSelected, $endDateSelected)->toArray();
            if (count($cars) === 0) {
                $driverBusy = [];
                continue;
            }

            $availableCars = [];
            foreach ($cars as $car) {
                if (is_null($car['start_date'])) {
                    $availableCar = $car;
                    $availableCar['available_schedules'] = $availableSchedules;
                    $availableCars[] = $availableCar;
                    continue;
                }

                $availableSchedulesCar = $availableSchedules;
                foreach ($availableSchedulesCar as $index => $time) {
                    if ($this->isScheduleBusy($time, Carbon::make($car['start_date']), Carbon::make($car['end_date']))) {
                        unset($availableSchedulesCar[$index]);
                        continue 2;
                    }
                }

                if (count($availableSchedulesCar) === 0) {
                    continue;
                }

                $availableCar = $car;
                $availableCar['available_schedules'] = $availableSchedulesCar;
                $availableCars[] = $availableCar;
            }

            if (count($availableCars) === 0) {
                $driverBusy = [];
                continue;
            }

            $availableDriver = $driver;
            $availableDriver['available_cars'] = $availableCars;
            $availableDrivers[] = $availableDriver;
            $driverBusy = [];
        }

        return collect($availableDrivers);
    }

    public function clearRelationWithCar(int $driverId, int $statusId): void
    {
        $user = $this->entityRepository->findById($driverId);
        if ($user->role->name !== NameRole::DRIVER) {
            return;
        }

        $status = $this->lookupRepository->findById($statusId);
        if ($status->code === StatusUserLookup::code(StatusUserLookup::INACTIVE)) {
            $this->carDriverRepository->deleteByDriverId($driverId);
        }
    }

    private function isScheduleBusy(array $time, Carbon $startTime, Carbon $endTime): bool
    {
        return (($time['start_time']->gte($startTime) && $time['start_time']->lt($endTime)) ||
            ($time['end_time']->gt($startTime) && $time['end_time']->lte($endTime)));
    }

    /**
     * @throws CustomErrorException
     */
    public function updateParcelDays(int $id, UserDTO $dto): void
    {
        $data = [];
        $now = now();
        foreach($dto->driverParcelDays as $driverParcelDay) {
            $values = array_merge($driverParcelDay->toArray(['day_id']), ['driver_id' => $id, 'created_at' => $now]);
            $data[] = new DriverParcelDay($values);
        }

        $this->entityRepository->syncParcelDays($id, $data);
    }
}