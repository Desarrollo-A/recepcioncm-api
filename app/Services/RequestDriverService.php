<?php

namespace App\Services;

use App\Contracts\Repositories\AddressRepositoryInterface;
use App\Contracts\Repositories\CancelRequestRepositoryInterface;
use App\Contracts\Repositories\CarScheduleRepositoryInterface;
use App\Contracts\Repositories\DriverRequestScheduleRepositoryInterface;
use App\Contracts\Repositories\DriverScheduleRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\RequestDriverRepositoryInterface;
use App\Contracts\Repositories\RequestDriverViewRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Services\CalendarServiceInterface;
use App\Contracts\Services\RequestDriverServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Message;
use App\Helpers\Enum\Path;
use App\Helpers\Enum\QueryParam;
use App\Helpers\File;
use App\Helpers\Validation;
use App\Models\Dto\CancelRequestDTO;
use App\Models\Dto\RequestDriverDTO;
use App\Models\Dto\RequestDTO;
use App\Models\Enums\Lookups\StatusDriverRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use App\Models\Lookup;
use App\Models\Request;
use App\Models\RequestDriver;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response as HttpCodes;
use Symfony\Component\HttpFoundation\Response;

class RequestDriverService extends BaseService implements RequestDriverServiceInterface
{
    protected $entityRepository;
    protected $requestRepository;
    protected $lookupRepository;
    protected $addressRepository;
    protected $requestDriverViewRepository;
    protected $cancelRequestRepository;
    protected $driverScheduleRepository;
    protected $carScheduleRepository;
    protected $driverRequestScheduleRepository;

    protected $calendarService;

    public function __construct(RequestDriverRepositoryInterface $requestDriverRepository,
                                RequestRepositoryInterface $requestRepository,
                                LookupRepositoryInterface $lookupRepository,
                                AddressRepositoryInterface $addressRepository,
                                RequestDriverViewRepositoryInterface $requestDriverViewRepository,
                                CancelRequestRepositoryInterface $cancelRequestRepository,
                                DriverScheduleRepositoryInterface $driverScheduleRepository,
                                CarScheduleRepositoryInterface $carScheduleRepository,
                                DriverRequestScheduleRepositoryInterface $driverRequestScheduleRepository,
                                CalendarServiceInterface $calendarService)
    {
        $this->entityRepository = $requestDriverRepository;
        $this->requestRepository = $requestRepository;
        $this->lookupRepository = $lookupRepository;
        $this->addressRepository = $addressRepository;
        $this->requestDriverViewRepository = $requestDriverViewRepository;
        $this->cancelRequestRepository = $cancelRequestRepository;
        $this->driverScheduleRepository = $driverScheduleRepository;
        $this->carScheduleRepository = $carScheduleRepository;
        $this->driverRequestScheduleRepository = $driverRequestScheduleRepository;
        $this->calendarService = $calendarService;
    }

    /**
     * @throws CustomErrorException
     */
    public function create(RequestDriverDTO $dto): RequestDriver
    {
        $pickupAddress = $this->addressRepository->create($dto->pickupAddress->toArray(['street', 'num_ext', 'num_int',
            'suburb', 'postal_code', 'state', 'country_id']));
        $dto->pickup_address_id = $pickupAddress->id;

        $arrivalAddress = $this->addressRepository->create($dto->arrivalAddress->toArray(['street', 'num_ext', 'num_int',
            'suburb', 'postal_code', 'state', 'country_id']));
        $dto->arrival_address_id = $arrivalAddress->id;

        $dto->request->status_id = $this->lookupRepository
            ->findByCodeAndType(StatusDriverRequestLookup::code(StatusDriverRequestLookup::NEW),
                TypeLookup::STATUS_DRIVER_REQUEST)
            ->id;
        $dto->request->type_id = $this->lookupRepository
            ->findByCodeAndType(TypeRequestLookup::code(TypeRequestLookup::DRIVER),
                TypeLookup::TYPE_REQUEST)
            ->id;

        $request = $this->requestRepository->create($dto->request->toArray(['title', 'start_date', 'end_date', 'comment',
            'type_id', 'add_google_calendar', 'user_id', 'status_id', 'people']));
        $dto->request_id = $request->id;

        $requestDriver = $this->entityRepository->create($dto->toArray(['pickup_address_id', 'arrival_address_id',
            'request_id', 'office_id']));
        return $requestDriver->fresh(['request', 'pickupAddress', 'arrivalAddress']);
    }

    /**
     * @throws CustomErrorException
     */
    public function uploadAuthorizationFile(int $id, RequestDriverDTO $dto): void
    {
        $dto->authorization_filename = File::uploadFile($dto->authorization_file, Path::DRIVER_AUTHORIZATION_DOCUMENTS);
        $this->entityRepository->update($id, $dto->toArray(['authorization_filename']));
    }

    /**
     * @throws CustomErrorException
     */
    public function findAllDriversPaginated(HttpRequest $request, User $user, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->requestDriverViewRepository->findAllDriversPaginated($filters, $perPage, $user, $sort);
    }

    public function findByDriverRequestId(int $id, User $user): RequestDriver
    {
        $driver = $this->entityRepository->findByRequestId($id);
        if ($user->role->name === NameRole::RECEPCIONIST) {
            if($user->office_id !== $driver->office_id){
                throw new AuthorizationException();
            }
        }elseif ($user->role->name === NameRole::APPLICANT) {
            if ($user->id !== $driver->request->user_id) {
                throw new AuthorizationException();
            }
        }
        return $driver;
    }

    /**
     * @throws CustomErrorException
     */
    public function getStatusByStatusCurrent(string $code, string $roleName): Collection
    {
        if (!in_array($code, StatusDriverRequestLookup::getAllCodes()->all())) {
            throw new CustomErrorException('No existe el estatus', HttpCodes::HTTP_NOT_FOUND);
        }

        $status = Collection::make();
        if ($roleName === NameRole::RECEPCIONIST) {
            switch ($code) {
                case StatusDriverRequestLookup::code(StatusDriverRequestLookup::NEW):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusDriverRequestLookup::code(StatusDriverRequestLookup::PROPOSAL),
                        StatusDriverRequestLookup::code(StatusDriverRequestLookup::APPROVED),
                        StatusDriverRequestLookup::code(StatusDriverRequestLookup::TRANSFER),
                        StatusDriverRequestLookup::code(StatusDriverRequestLookup::CANCELLED)
                    ], TypeLookup::STATUS_PACKAGE_REQUEST);
                    break;
                case StatusDriverRequestLookup::code(StatusDriverRequestLookup::APPROVED):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusDriverRequestLookup::code(StatusDriverRequestLookup::CANCELLED)
                    ], TypeLookup::STATUS_PACKAGE_REQUEST);
                    break;
            }
        } else if ($roleName === NameRole::APPLICANT) {
            switch ($code) {
                case StatusDriverRequestLookup::code(StatusDriverRequestLookup::PROPOSAL):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusDriverRequestLookup::code(StatusDriverRequestLookup::REJECTED)
                    ], TypeLookup::STATUS_PACKAGE_REQUEST);
                    break;
                case StatusDriverRequestLookup::code(StatusDriverRequestLookup::APPROVED):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusDriverRequestLookup::code(StatusDriverRequestLookup::CANCELLED)
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
            StatusDriverRequestLookup::code(StatusDriverRequestLookup::NEW),
            StatusDriverRequestLookup::code(StatusDriverRequestLookup::APPROVED),
        ], TypeLookup::STATUS_DRIVER_REQUEST);

        $request = $this->requestRepository->findById($dto->request_id);

        if (!in_array($request->status_id, $status->pluck('id')->toArray())) {
            throw new CustomErrorException('La solicitud debe estar en estatus '
                .StatusDriverRequestLookup::code(StatusDriverRequestLookup::NEW).' o '
                .StatusDriverRequestLookup::code(StatusDriverRequestLookup::APPROVED),
                HttpCodes::HTTP_BAD_REQUEST);
        }

        $cancelStatusId = $this->lookupRepository
            ->findByCodeAndType(StatusDriverRequestLookup::code(StatusDriverRequestLookup::CANCELLED),
                TypeLookup::STATUS_DRIVER_REQUEST)
            ->id;

        $requestDTO = new RequestDTO(['status_id' => $cancelStatusId]);

        if (config('app.enable_google_calendar', false)) {
            $this->calendarService->deleteEvent($request->event_google_calendar_id);
        }

        $lastStatusId = $request->status_id;

        $statusApproved = $status->first(function (Lookup $lookup) {
            return $lookup->code === StatusDriverRequestLookup::code(StatusDriverRequestLookup::APPROVED);
        });

        // Si la solicitud fue aprobada anteriormente
        if ($lastStatusId === $statusApproved->id) {
            $requestDriver = $this->entityRepository->findByRequestId($dto->request_id);
            $this->driverRequestScheduleRepository->deleteByRequestDriverId($requestDriver->id);
            $this->carScheduleRepository->delete($requestDriver->driverRequestSchedule->carSchedule->id);
            $this->driverScheduleRepository->delete($requestDriver->driverRequestSchedule->driverSchedule->id);
        }

        $request = $this->requestRepository->update($dto->request_id, $requestDTO->toArray(['status_id', 'event_google_calendar_id']));

        $this->cancelRequestRepository->create($dto->toArray(['request_id', 'cancel_comment', 'user_id']));

        return $request;
    }

    /**
     * @throws CustomErrorException
     */
    public function transferRequest(int $requestDriverId, RequestDriverDTO $dto): RequestDriver
    {
        return $this->entityRepository->update($requestDriverId, $dto->toArray(['office_id']));
    }

    /**
     * @throws CustomErrorException
     */
    public function approvedRequest(RequestDriverDTO $dto): Request
    {
        $dto->request->status_id = $this->lookupRepository
            ->findByCodeAndType(StatusDriverRequestLookup::code(StatusDriverRequestLookup::APPROVED),
                TypeLookup::STATUS_DRIVER_REQUEST)
            ->id;

        $request = $this->requestRepository->findById($dto->request_id);
        $this->requestRepository->update($dto->request_id, $dto->request->toArray(['status_id']));

        $dto->driverRequestSchedule->carSchedule->start_date = $request->start_date;
        $dto->driverRequestSchedule->carSchedule->end_date = $request->end_date;
        $carSchedule = $this->carScheduleRepository
            ->create($dto->driverRequestSchedule->carSchedule->toArray(['car_id', 'start_date', 'end_date']));

        $dto->driverRequestSchedule->driverSchedule->start_date = $request->start_date;
        $dto->driverRequestSchedule->driverSchedule->end_date = $request->end_date;
        $driverSchedule = $this->driverScheduleRepository
            ->create($dto->driverRequestSchedule->driverSchedule->toArray(['driver_id', 'start_date', 'end_date']));

        $dto->driverRequestSchedule->driver_schedule_id = $driverSchedule->id;
        $dto->driverRequestSchedule->car_schedule_id = $carSchedule->id;
        $this->driverRequestScheduleRepository
            ->create($dto->driverRequestSchedule->toArray(['request_driver_id', 'driver_schedule_id', 'car_schedule_id']));

        return $request;
    }
}