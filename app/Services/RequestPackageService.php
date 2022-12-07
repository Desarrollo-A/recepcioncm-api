<?php

namespace App\Services;

use App\Contracts\Repositories\AddressRepositoryInterface;
use App\Contracts\Repositories\CancelRequestRepositoryInterface;
use App\Contracts\Repositories\CarScheduleRepositoryInterface;
use App\Contracts\Repositories\DriverPackageScheduleRepositoryInterface;
use App\Contracts\Repositories\DriverScheduleRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\PackageRepositoryInterface;
use App\Contracts\Repositories\RequestPackageViewRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Repositories\ScoreRepositoryInterface;
use App\Contracts\Services\CalendarServiceInterface;
use App\Contracts\Services\RequestPackageServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Path;
use App\Helpers\Enum\QueryParam;
use App\Helpers\File;
use App\Helpers\Validation;
use App\Models\Dto\CancelRequestDTO;
use App\Models\Dto\PackageDTO;
use App\Models\Dto\RequestDTO;
use App\Models\Dto\ScoreDTO;
use App\Models\Enums\Lookups\StatusPackageRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use App\Models\Lookup;
use App\Models\Package;
use App\Models\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response as HttpCodes;

class RequestPackageService extends BaseService implements RequestPackageServiceInterface
{
    public $START_TIME_WORKING = '08:00:00.000';
    public $END_TIME_WORKING = '18:00:00.000';

    protected $packageRepository;
    protected $requestRepository;
    protected $lookupRepository;
    protected $addressRepository;
    protected $requestPackageViewRepository;
    protected $scoreRepository;
    protected $cancelRequestRepository;
    protected $driverScheduleRepository;
    protected $carScheduleRepository;
    protected $driverPackageScheduleRepository;

    protected $calendarService;

    public function __construct(RequestRepositoryInterface $requestRepository,
                                PackageRepositoryInterface $packageRepository,
                                LookupRepositoryInterface $lookupRepository,
                                AddressRepositoryInterface $addressRepository,
                                RequestPackageViewRepositoryInterface $requestPackageViewRepository,
                                ScoreRepositoryInterface $scoreRepository,
                                CancelRequestRepositoryInterface $cancelRequestRepository,
                                CalendarServiceInterface $calendarService,
                                DriverScheduleRepositoryInterface $driverScheduleRepository,
                                CarScheduleRepositoryInterface $carScheduleRepository,
                                DriverPackageScheduleRepositoryInterface $driverPackageScheduleRepository)
    {
        $this->requestRepository = $requestRepository;
        $this->packageRepository = $packageRepository;
        $this->lookupRepository = $lookupRepository;
        $this->addressRepository = $addressRepository;
        $this->requestPackageViewRepository = $requestPackageViewRepository;
        $this->scoreRepository = $scoreRepository;
        $this->cancelRequestRepository = $cancelRequestRepository;
        $this->calendarService = $calendarService;
        $this->driverScheduleRepository = $driverScheduleRepository;
        $this->carScheduleRepository = $carScheduleRepository;
        $this->driverPackageScheduleRepository = $driverPackageScheduleRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function createRequestPackage(PackageDTO $dto): Package
    {
        $pickupAddress = $this->addressRepository->create($dto->pickupAddress->toArray(['street', 'num_ext', 'num_int',
            'suburb', 'postal_code', 'state', 'country_id']));
        $dto->pickup_address_id = $pickupAddress->id;

        $arrivalAddress = $this->addressRepository->create($dto->arrivalAddress->toArray(['street', 'num_ext', 'num_int',
            'suburb', 'postal_code', 'state', 'country_id']));
        $dto->arrival_address_id = $arrivalAddress->id;

        $dto->request->status_id = $this->lookupRepository
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::NEW),
                TypeLookup::STATUS_PACKAGE_REQUEST)
            ->id;

        $dto->request->type_id = $this->lookupRepository
            ->findByCodeAndType(TypeRequestLookup::code(TypeRequestLookup::PARCEL),
            TypeLookup::TYPE_REQUEST)
            ->id;

        $request = $this->requestRepository->create($dto->request->toArray(['title', 'start_date', 'comment', 'type_id',
            'add_google_calendar', 'user_id', 'status_id']));
        $dto->request_id = $request->id;

        $package = $this->packageRepository->create($dto->toArray(['pickup_address_id', 'arrival_address_id',
            'name_receive', 'email_receive', 'comment_receive', 'request_id', 'office_id']));
        return $package->fresh(['request', 'pickupAddress', 'arrivalAddress']);
    }

    /**
     * @throws CustomErrorException
     */
    public function uploadAuthorizationFile(int $id, PackageDTO $dto): void
    {
        $dto->authorization_filename = File::uploadFile($dto->authorization_file, Path::PACKAGE_AUTHORIZATION_DOCUMENTS);
        $this->packageRepository->update($id, $dto->toArray(['authorization_filename']));
    }

    /**
     * @throws CustomErrorException
     */
    public function findAllRoomsPaginated(HttpRequest $request, User $user, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->requestPackageViewRepository->findAllPackagesPaginated($filters, $perPage, $user, $sort);
    }

    public function findById(int $id): Package
    {
        return $this->packageRepository->findById($id);
    }

    /**
     * @throws CustomErrorException
     */
    public function getStatusByStatusCurrent(string $code, string $roleName): Collection
    {
        if (!in_array($code, StatusPackageRequestLookup::getAllCodes()->all())) {
            throw new CustomErrorException('No existe el estatus', HttpCodes::HTTP_NOT_FOUND);
        }

        $status = Collection::make([]);
        if ($roleName === NameRole::RECEPCIONIST) {
            switch ($code) {
                case StatusPackageRequestLookup::code(StatusPackageRequestLookup::NEW):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusPackageRequestLookup::code(StatusPackageRequestLookup::PROPOSAL),
                        StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED),
                        StatusPackageRequestLookup::code(StatusPackageRequestLookup::TRANSFER),
                        StatusPackageRequestLookup::code(StatusPackageRequestLookup::CANCELLED)
                    ], TypeLookup::STATUS_PACKAGE_REQUEST);
                    break;
                case StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusPackageRequestLookup::code(StatusPackageRequestLookup::ROAD),
                        StatusPackageRequestLookup::code(StatusPackageRequestLookup::CANCELLED)
                    ], TypeLookup::STATUS_PACKAGE_REQUEST);
                    break;
            }
        } else if ($roleName === NameRole::APPLICANT) {
            switch ($code) {
                case StatusPackageRequestLookup::code(StatusPackageRequestLookup::PROPOSAL):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusPackageRequestLookup::code(StatusPackageRequestLookup::REJECTED)
                    ], TypeLookup::STATUS_PACKAGE_REQUEST);
                    break;
                case StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusPackageRequestLookup::code(StatusPackageRequestLookup::CANCELLED)
                    ], TypeLookup::STATUS_PACKAGE_REQUEST);
                    break;
            }
        }

        return $status;
    }

    /**
     * @throws CustomErrorException
     */
    public function cancelRequest(CancelRequestDTO $dto): Request
    {
        $status = $this->lookupRepository->findByCodeWhereInAndType([
            StatusPackageRequestLookup::code(StatusPackageRequestLookup::NEW),
            StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED),
        ], TypeLookup::STATUS_PACKAGE_REQUEST);

        $request = $this->requestRepository->findById($dto->request_id);

        if (!in_array($request->status_id, $status->pluck('id')->toArray())) {
            throw new CustomErrorException('La solicitud debe estar en estatus '
                .StatusPackageRequestLookup::code(StatusPackageRequestLookup::NEW).' o '
                .StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED),
                HttpCodes::HTTP_BAD_REQUEST);
        }

        $cancelStatusId = $this->lookupRepository
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::CANCELLED),
                TypeLookup::STATUS_PACKAGE_REQUEST)
            ->id;

        $requestDTO = new RequestDTO(['status_id' => $cancelStatusId]);

        if (config('app.enable_google_calendar', false)) {
            $this->calendarService->deleteEvent($request->event_google_calendar_id);
        }

        $lastStatusId = $request->status_id;

        $request = $this->requestRepository->update($dto->request_id, $requestDTO->toArray(['status_id', 'event_google_calendar_id']));

        $this->cancelRequestRepository->create($dto->toArray(['request_id', 'cancel_comment', 'user_id']));

        $statusApproved = $status->first(function (Lookup $lookup) {
            return $lookup->code === StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED);
        });

        // Si la solicitud fue aprobada anteriormente
        if ($lastStatusId === $statusApproved->id) {
            $package = $this->packageRepository->findByRequestId($dto->request_id);
            if (is_null($package->tracking_code)) {
                $this->driverPackageScheduleRepository->deleteByPackageId($package->id);
                $this->carScheduleRepository->delete($package->driverPackageSchedule->carSchedule->id);
                $this->driverScheduleRepository->delete($package->driverPackageSchedule->driverSchedule->id);
            }

            $this->packageRepository->update($package->id, (new PackageDTO())
                ->toArray(['tracking_code', 'url_tracking', 'auth_code']));
        }

        return $request->fresh(['package']);
    }

    /**
     * @throws CustomErrorException
     */
    public function transferRequest(int $packageId, PackageDTO $dto): void
    {
        $this->packageRepository->update($packageId, $dto->toArray(['office_id']));
    }

    public function getScheduleDriver(int $officeId): Collection
    {
        return $this->driverPackageScheduleRepository->getScheduleDriverPackage($officeId);
    }

    public function getPackagesByDriverId(int $driverId, Carbon $date): Collection
    {
        return $this->packageRepository->getPackagesByDriverId($driverId, $date);
    }

    /**
     * @throws CustomErrorException
     */
    public function approvedRequestPackage(PackageDTO $dto): void
    {
        $dto->request->status_id = $this->lookupRepository
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED),
                TypeLookup::STATUS_PACKAGE_REQUEST)
            ->id;

        if (is_null($dto->tracking_code)) {
            $request = $this->requestRepository->findById($dto->request_id);

            $startDate = "{$request->start_date->toDateString()} $this->START_TIME_WORKING";
            $endDate = "{$request->start_date->toDateString()} $this->END_TIME_WORKING";

            $dto->request->end_date = $endDate;
            $this->requestRepository->update($dto->request_id, $dto->request->toArray(['status_id', 'end_date']));

            // TODO: Agregar la parte del código para mandar el comentario en la URL

            $dto->driverPackageSchedule->carSchedule->start_date = $startDate;
            $dto->driverPackageSchedule->carSchedule->end_date = $endDate;
            $carSchedule = $this->carScheduleRepository
                ->create($dto->driverPackageSchedule->carSchedule->toArray(['car_id', 'start_date', 'end_date']));

            $dto->driverPackageSchedule->driverSchedule->start_date = $startDate;
            $dto->driverPackageSchedule->driverSchedule->end_date = $endDate;
            $driverSchedule = $this->driverScheduleRepository
                ->create($dto->driverPackageSchedule->driverSchedule->toArray(['driver_id', 'start_date', 'end_date']));

            $dto->driverPackageSchedule->driver_schedule_id = $driverSchedule->id;
            $dto->driverPackageSchedule->car_schedule_id = $carSchedule->id;
            $this->driverPackageScheduleRepository
                ->create($dto->driverPackageSchedule->toArray(['package_id', 'driver_schedule_id', 'car_schedule_id']));
        } else {
            $this->requestRepository->update($dto->request_id, $dto->request->toArray(['status_id', 'end_date']));
            $this->packageRepository->update($dto->id, $dto->toArray(['tracking_code', 'url_tracking']));
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function insertScore(ScoreDTO $score): void
    {
        $typeRequestId = $this->requestRepository->findById($score->request_id);
        $typeStatusId = $this->lookupRepository
            ->findByCodeAndType(TypeRequestLookup::code(TypeRequestLookup::PARCEL),
                TypeLookup::TYPE_REQUEST)
            ->id;

        if ($typeRequestId->type_id !== $typeStatusId) {
            throw new CustomErrorException("El tipo de solicitud debe ser de paquetería", HttpCodes::HTTP_BAD_REQUEST);
        }

        $statusPackageId = $this->lookupRepository
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::ROAD),
                TypeLookup::STATUS_PACKAGE_REQUEST)
            ->id;
        if ($typeRequestId->status_id !== $statusPackageId) {
            throw new CustomErrorException("El estatus de solicitud debe estar ".StatusPackageRequestLookup::ROAD,
                HttpCodes::HTTP_BAD_REQUEST);
        }

        $statusId = $this->lookupRepository
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::DELIVERED),
                TypeLookup::STATUS_PACKAGE_REQUEST)
            ->id;
        $request = new RequestDTO(['status_id' => $statusId]);
        $this->requestRepository->update($score->request_id, $request->toArray(['status_id']));
        $packageId = $this->packageRepository->findByRequestId($typeRequestId->id)->id;
        $this->packageRepository->update($packageId, ['auth_code' => null]);
        $this->scoreRepository->create($score->toArray(['request_id', 'score', 'comment']));
    }

    public function isPackageCompleted(int $requestPackageId): bool
    {
        $typePackageRequestId = $this->lookupRepository
            ->findByCodeAndType(TypeRequestLookup::code(TypeRequestLookup::PARCEL),
                TypeLookup::TYPE_REQUEST)->id;

        $packageRequestData = $this->requestRepository->findById($requestPackageId);

        if ($packageRequestData->type_id !== $typePackageRequestId) {
            throw new ModelNotFoundException();
        }

        $statusPackageCancelledId = $this->lookupRepository
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::CANCELLED),
                TypeLookup::STATUS_PACKAGE_REQUEST)->id;

        if ($packageRequestData->status_id === $statusPackageCancelledId) {
            throw new CustomErrorException('La solicitud está cancelada.', HttpCodes::HTTP_NOT_FOUND);
        }

        $statusPackageRoadId = $this->lookupRepository
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::ROAD),
                TypeLookup::STATUS_PACKAGE_REQUEST)->id;

        return $statusPackageRoadId === $packageRequestData->status_id;
    }

    public function isAuthPackage(string $authCodePackage): bool
    {
        $findAuthCodePackage = $this->packageRepository->findByAuthCode($authCodePackage);
        return !is_null($findAuthCodePackage);

    }

    public function findByRequestId(int $requestId): Package{
        return $this->packageRepository->findByRequestId($requestId);
    }
}