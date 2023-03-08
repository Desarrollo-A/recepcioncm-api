<?php

namespace App\Services;

use App\Contracts\Repositories\AddressRepositoryInterface;
use App\Contracts\Repositories\CancelRequestRepositoryInterface;
use App\Contracts\Repositories\CarScheduleRepositoryInterface;
use App\Contracts\Repositories\DriverRequestScheduleRepositoryInterface;
use App\Contracts\Repositories\DriverScheduleRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\ProposalRequestRepositoryInterface;
use App\Contracts\Repositories\RequestDriverRepositoryInterface;
use App\Contracts\Repositories\RequestDriverViewRepositoryInterface;
use App\Contracts\Repositories\RequestEmailRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\CalendarServiceInterface;
use App\Contracts\Services\RequestDriverServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Path;
use App\Helpers\Enum\QueryParam;
use App\Helpers\File;
use App\Helpers\Validation;
use App\Mail\RequestDriver\ApprovedRequestDriverInformationMail;
use App\Mail\RequestDriver\CancelledRequestDriverInformationMail;
use App\Models\Dto\CancelRequestDTO;
use App\Models\Dto\ProposalRequestDTO;
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
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response as HttpCodes;

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
    protected $proposalRequestRepository;
    protected $userRepository;
    protected $requestEmailRepository;

    protected $calendarService;

    public function __construct(
        RequestDriverRepositoryInterface $requestDriverRepository,
        RequestRepositoryInterface $requestRepository,
        LookupRepositoryInterface $lookupRepository,
        AddressRepositoryInterface $addressRepository,
        RequestDriverViewRepositoryInterface $requestDriverViewRepository,
        CancelRequestRepositoryInterface $cancelRequestRepository,
        DriverScheduleRepositoryInterface $driverScheduleRepository,
        CarScheduleRepositoryInterface $carScheduleRepository,
        DriverRequestScheduleRepositoryInterface $driverRequestScheduleRepository,
        CalendarServiceInterface $calendarService,
        ProposalRequestRepositoryInterface $proposalRequestRepository,
        RequestEmailRepositoryInterface $requestEmailRepository,
        UserRepositoryInterface $userRepository
    )
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
        $this->proposalRequestRepository = $proposalRequestRepository;
        $this->requestEmailRepository = $requestEmailRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function create(RequestDriverDTO $dto): RequestDriver
    {
        if(is_null($dto->pickup_address_id) || !isset($dto->pickup_address_id)){
            $pickupAddress = $this->addressRepository->create($dto->pickupAddress->toArray(['street', 'num_ext', 'num_int',
                'suburb', 'postal_code', 'state', 'country_id']));
            $dto->pickup_address_id = $pickupAddress->id;
        }
        if(is_null($dto->arrival_address_id) || !isset($dto->arrival_address_id)){
            $arrivalAddress = $this->addressRepository->create($dto->arrivalAddress->toArray(['street', 'num_ext', 'num_int',
                'suburb', 'postal_code', 'state', 'country_id']));
            $dto->arrival_address_id = $arrivalAddress->id;
        }

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

        if (count($dto->request->requestEmail) > 0) {
            $emailsInsert = array();
            foreach ($dto->request->requestEmail as $data) {
                $data->request_id = $request->id;
                $emailsInsert[] = $data->toArray(['request_id', 'name', 'email', 'created_at', 'updated_at']);
            }
            $this->requestEmailRepository->bulkInsert($emailsInsert);
        }

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

    /**
     * @throws AuthorizationException
     */
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
                    ], TypeLookup::STATUS_DRIVER_REQUEST);
                    break;
                case StatusDriverRequestLookup::code(StatusDriverRequestLookup::APPROVED):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusDriverRequestLookup::code(StatusDriverRequestLookup::CANCELLED)
                    ], TypeLookup::STATUS_DRIVER_REQUEST);
                    break;
            }
        } else if ($roleName === NameRole::APPLICANT) {
            switch ($code) {
                case StatusDriverRequestLookup::code(StatusDriverRequestLookup::PROPOSAL):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusDriverRequestLookup::code(StatusDriverRequestLookup::REJECTED),
                        StatusDriverRequestLookup::code(StatusDriverRequestLookup::ACCEPTED)
                    ], TypeLookup::STATUS_DRIVER_REQUEST);
                    break;
                case StatusDriverRequestLookup::code(StatusDriverRequestLookup::APPROVED):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusDriverRequestLookup::code(StatusDriverRequestLookup::CANCELLED)
                    ], TypeLookup::STATUS_DRIVER_REQUEST);
                    break;
            }
        }

        return $status;
    }

    /**
     * @param CancelRequestDTO $dto
     * @return object {request: App\Models\Request, driverId: number}
     * @throws CustomErrorException
     */
    public function cancelRequest(CancelRequestDTO $dto): object
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

        $this->cancelRequestRepository->create($dto->toArray(['request_id', 'cancel_comment', 'user_id']));

        $request = $this->requestRepository->update($dto->request_id, $requestDTO->toArray(['status_id', 'event_google_calendar_id']))
            ->fresh(['status', 'cancelRequest', 'requestDriver', 'requestDriver.driverRequestSchedule.carSchedule.car',
                    'requestDriver.driverRequestSchedule.driverSchedule.driver']);
        
        // Si la solicitud fue aprobada anteriormente
        $driverId = null;
        if ($lastStatusId === $statusApproved->id) {
            
            $emailDriver = $request->requestDriver->driverRequestSchedule->driverSchedule->driver->email;
            Mail::send(new CancelledRequestDriverInformationMail($request, $emailDriver));

            $requestDriver = $this->entityRepository->findByRequestId($dto->request_id);
            $driverId = $requestDriver->driverRequestSchedule->driverSchedule->driver_id;

            $this->driverRequestScheduleRepository->deleteByRequestDriverId($requestDriver->id);
            $this->carScheduleRepository->delete($requestDriver->driverRequestSchedule->carSchedule->id);
            $this->driverScheduleRepository->delete($requestDriver->driverRequestSchedule->driverSchedule->id);
        }
        
        return (object)[
            'request' => $request,
            'driverId' => $driverId
        ];
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

        $request = $this->requestRepository->update($dto->request_id, $dto->request->toArray(['status_id']))
            ->fresh(['requestDriver', 'requestDriver.driverRequestSchedule',
                'requestDriver.driverRequestSchedule.driverSchedule','requestDriver.driverRequestSchedule.carSchedule',
                'requestDriver.driverRequestSchedule.driverSchedule.driver',
                'requestDriver.driverRequestSchedule.carSchedule.car' , 'requestDriver.pickupAddress',
                'requestDriver.arrivalAddress', 'status']);
        
        $emailDriver = $this->userRepository->findById($dto->driverRequestSchedule->driverSchedule->driver_id)->email;
        Mail::send(new ApprovedRequestDriverInformationMail($request, $emailDriver));

        if (config('app.enable_google_calendar', false)) {
            $emails[] = $emailDriver;
            $emails[] = $this->userRepository->findByOfficeIdAndRoleRecepcionist($request->requestDriver->office_id)->id;

            if ($request->add_google_calendar) {
                $emails[] = $request->user->email;
            }

            $event = $this->calendarService->createEvent($request->title, $request->start_date, $request->end_date, $emails);

            $dto = new RequestDTO([
                'event_google_calendar_id' => $event->id
            ]);
            $this->requestRepository->update($request->id, $dto->toArray(['event_google_calendar_id']));
        }

        return $request;
    }

    /**
     * @throws CustomErrorException
     */
    public function findAllByDriverIdPaginated(HttpRequest $request, User $user, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->requestDriverViewRepository->findAllByDriverIdPaginated($filters, $perPage, $user, $sort);
    }

    public function getBusyDaysForProposalCalendar(): array
    {
        $data = $this->driverRequestScheduleRepository->getBusyDaysForProposalCalendar()
            ->map(function ($values) {
                return ['start_date' => $values->start_date, 'end_date' => $values->end_date];
            })
            ->flatten()
            ->map(function ($date) {
                return "$date 00:00:00";
            })
            ->toArray();

        return array_values(array_unique($data));
    }

    /**
     * @throws CustomErrorException
     */
    public function proposalRequest(RequestDriverDTO $dto): Request
    {
        $dto->request->status_id = $this->lookupRepository
            ->findByCodeAndType(StatusDriverRequestLookup::code(StatusDriverRequestLookup::PROPOSAL),
                TypeLookup::STATUS_DRIVER_REQUEST)
            ->id;

        $dto->driverRequestSchedule->request_driver_id = $this->entityRepository->findByRequestId($dto->request_id)->id;
        $request = $this->requestRepository->update($dto->request_id, $dto->request->toArray(['status_id']));

        $carSchedule = $this->carScheduleRepository
            ->create($dto->driverRequestSchedule->carSchedule->toArray(['car_id', 'start_date', 'end_date']));

        $driverSchedule = $this->driverScheduleRepository
            ->create($dto->driverRequestSchedule->driverSchedule->toArray(['driver_id', 'start_date', 'end_date']));

        $dto->driverRequestSchedule->driver_schedule_id = $driverSchedule->id;
        $dto->driverRequestSchedule->car_schedule_id = $carSchedule->id;
        $this->driverRequestScheduleRepository
            ->create($dto->driverRequestSchedule->toArray(['request_driver_id', 'driver_schedule_id', 'car_schedule_id']));

        $proposalDTO = new ProposalRequestDTO([
            'request_id' => $dto->request_id,
            'start_date' => $dto->request->start_date,
            'end_date' => $dto->request->end_date
        ]);

        $this->proposalRequestRepository->create($proposalDTO->toArray(['request_id', 'start_date', 'end_date']));

        return $request;
    }

    /**
     * @throws CustomErrorException
     */
    public function responseRejectRequest(int $requestId, RequestDTO $dto): Request
    {
        $proposalStatusId = $this->lookupRepository->findByCodeAndType(StatusDriverRequestLookup::code(
            StatusDriverRequestLookup::PROPOSAL), TypeLookup::STATUS_DRIVER_REQUEST)->id;

        $request = $this->requestRepository->findById($requestId);

        if ($request->status_id !== $proposalStatusId) {
            throw new CustomErrorException('La solicitud debe de estar en estatus '.StatusDriverRequestLookup::PROPOSAL,
                HttpCodes::HTTP_BAD_REQUEST);
        }

        $statusCode = ($dto->status->code === StatusDriverRequestLookup::code(StatusDriverRequestLookup::ACCEPTED))
            ? StatusDriverRequestLookup::code(StatusDriverRequestLookup::APPROVED)
            : $dto->status->code;
        $dto->status = $this->lookupRepository->findByCodeAndType($statusCode, TypeLookup::STATUS_DRIVER_REQUEST);
        $dto->status_id = $dto->status->id;

        if ($dto->status->code === StatusDriverRequestLookup::code(StatusDriverRequestLookup::REJECTED)) {
            $requestDriver = $this->entityRepository->findByRequestId($requestId);
            $this->driverRequestScheduleRepository->deleteByRequestDriverId($requestDriver->id);
            $this->carScheduleRepository->delete($requestDriver->driverRequestSchedule->carSchedule->id);
            $this->driverScheduleRepository->delete($requestDriver->driverRequestSchedule->driverSchedule->id);

            $request = $this->requestRepository->update($requestId, $dto->toArray(['status_id']))
                ->fresh(['status', 'requestDriver']);
        } else {
            $proposalRequest = $this->proposalRequestRepository->findOneByRequestId($requestId);
            $dto->start_date = $proposalRequest->start_date;
            $dto->end_date = $proposalRequest->end_date;

            $request = $this->requestRepository->update($requestId, $dto->toArray(['status_id', 'start_date', 'end_date']))
                ->fresh(['status', 'requestDriver']);
        }

        $this->proposalRequestRepository->deleteByRequestId($requestId);

        return $request;
    }
}