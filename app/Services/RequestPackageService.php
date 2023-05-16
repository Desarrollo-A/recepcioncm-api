<?php

namespace App\Services;

use App\Contracts\Repositories\AddressRepositoryInterface;
use App\Contracts\Repositories\CancelRequestRepositoryInterface;
use App\Contracts\Repositories\CarScheduleRepositoryInterface;
use App\Contracts\Repositories\DeliveredPackageRepositoryInterface;
use App\Contracts\Repositories\DetailExternalParcelRepositoryInterface;
use App\Contracts\Repositories\DriverPackageScheduleRepositoryInterface;
use App\Contracts\Repositories\DriverScheduleRepositoryInterface;
use App\Contracts\Repositories\HeavyShipmentRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\PackageRepositoryInterface;
use App\Contracts\Repositories\ProposalPackageRepositoryInterface;
use App\Contracts\Repositories\ProposalRequestRepositoryInterface;
use App\Contracts\Repositories\RequestPackageViewRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Repositories\ScoreRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\CalendarServiceInterface;
use App\Contracts\Services\RequestPackageServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Path;
use App\Helpers\Enum\QueryParam;
use App\Helpers\File;
use App\Helpers\Validation;
use App\Mail\RequestPackage\ApprovedPackageMail;
use App\Mail\RequestPackage\ApprovedRequestPackageInformationMail;
use App\Mail\RequestPackage\CancelledRequestPackageInformationMail;
use App\Models\Dto\CancelRequestDTO;
use App\Models\Dto\DeliveredPackageDTO;
use App\Models\Dto\PackageDTO;
use App\Models\Dto\RequestDTO;
use App\Models\Dto\ScoreDTO;
use App\Models\Enums\Lookups\StatusPackageRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\PathRouteRecepcionist;
use App\Models\Enums\TypeLookup;
use App\Models\Lookup;
use App\Models\Package;
use App\Models\Request;
use App\Models\User;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as HttpCodes;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
    protected $proposalRequestRepository;
    protected $deliveredPackageRepository;

    protected $calendarService;
    protected $userRepository;
    protected $proposalPackageRepository;
    protected $heavyShipmentRepository;
    protected $detailExternalParcelRepository;
    protected $userService;

    public function __construct(
        RequestRepositoryInterface $requestRepository,
        PackageRepositoryInterface $packageRepository,
        LookupRepositoryInterface $lookupRepository,
        AddressRepositoryInterface $addressRepository,
        RequestPackageViewRepositoryInterface $requestPackageViewRepository,
        ScoreRepositoryInterface $scoreRepository,
        CancelRequestRepositoryInterface $cancelRequestRepository,
        CalendarServiceInterface $calendarService,
        DriverScheduleRepositoryInterface $driverScheduleRepository,
        CarScheduleRepositoryInterface $carScheduleRepository,
        DriverPackageScheduleRepositoryInterface $driverPackageScheduleRepository,
        ProposalRequestRepositoryInterface $proposalRequestRepository,
        DeliveredPackageRepositoryInterface $deliveredPackageRepository,
        UserRepositoryInterface $userRepository,
        ProposalPackageRepositoryInterface $proposalPackageRepository,
        HeavyShipmentRepositoryInterface $heavyShipmentRepository,
        DetailExternalParcelRepositoryInterface $detailExternalParcelRepository,
        UserServiceInterface $userService
    )
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
        $this->deliveredPackageRepository = $deliveredPackageRepository;
        $this->proposalRequestRepository = $proposalRequestRepository;
        $this->userRepository = $userRepository;
        $this->proposalPackageRepository = $proposalPackageRepository;
        $this->heavyShipmentRepository = $heavyShipmentRepository;
        $this->detailExternalParcelRepository = $detailExternalParcelRepository;
        $this->userService = $userService;
    }

    /**
     * @throws CustomErrorException
     */
    public function createRequestPackage(PackageDTO $dto): Package
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
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::IN_REVIEW_MANAGER),
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
            'name_receive', 'email_receive', 'comment_receive', 'request_id', 'office_id', 'is_urgent',
            'is_heavy_shipping']));

        if (count($dto->heavyShipments) > 0) {
            $heavyShipments = [];
            foreach ($dto->heavyShipments as $heavyShipment) {
                $heavyShipment->package_id = $package->id;

                $heavyShipments[] = $heavyShipment->toArray([
                    'package_id', 'high', 'long', 'width', 'description', 'created_at', 'updated_at'
                ]);
            }

            $this->heavyShipmentRepository->bulkInsert($heavyShipments);
        }

        return $package->fresh(['request', 'pickupAddress', 'arrivalAddress']);
    }

    /**
     * @throws CustomErrorException
     */
    public function findAllPackagesPaginated(HttpRequest $request, User $user, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->requestPackageViewRepository->findAllPackagesPaginated($filters, $perPage, $user, $sort);
    }

    /**
     * @throws AuthorizationException
     */
    public function findByPackageRequestId(int $id, User $user): Package
    {
        $package = $this->packageRepository->findByRequestId($id);
        $roleName = $user->role->name;
        if ($roleName === NameRole::RECEPCIONIST) {
            if($user->office_id !== $package->office_id){
                throw new AuthorizationException();
            }
        }elseif ($roleName === NameRole::APPLICANT) {
            if ($user->id !== $package->request->user_id) {
               throw new AuthorizationException();
            }
        } else if ($roleName === NameRole::DRIVER) {
            if ($package->request->status->code !== StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED) &&
                $package->request->status->code !== StatusPackageRequestLookup::code(StatusPackageRequestLookup::ROAD)) {
                throw new AuthorizationException();
            }
        }
        return $package;
    }

    /**
     * @throws CustomErrorException
     */
    public function getStatusByStatusCurrent(string $code, string $roleName): Collection
    {
        if (!in_array($code, StatusPackageRequestLookup::getAllCodes()->all())) {
            throw new CustomErrorException('No existe el estatus', HttpCodes::HTTP_NOT_FOUND);
        }

        $status = Collection::make();
        if ($roleName === NameRole::RECEPCIONIST) {
            switch ($code) {
                case StatusPackageRequestLookup::code(StatusPackageRequestLookup::NEW):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED),
                        StatusPackageRequestLookup::code(StatusPackageRequestLookup::TRANSFER)
                    ], TypeLookup::STATUS_PACKAGE_REQUEST);
                    break;
                case StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusPackageRequestLookup::code(StatusPackageRequestLookup::CANCELLED)
                    ], TypeLookup::STATUS_PACKAGE_REQUEST);
                    break;
                case StatusPackageRequestLookup::code(StatusPackageRequestLookup::IN_REVIEW):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED)
                    ], TypeLookup::STATUS_PACKAGE_REQUEST);
            }
        } else if ($roleName === NameRole::DEPARTMENT_MANAGER) {
            switch ($code) {
                case StatusPackageRequestLookup::code(StatusPackageRequestLookup::IN_REVIEW_MANAGER):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusPackageRequestLookup::code(StatusPackageRequestLookup::ACCEPT),
                        StatusPackageRequestLookup::code(StatusPackageRequestLookup::CANCELLED)
                    ], TypeLookup::STATUS_PACKAGE_REQUEST);
                    break;
            }
        }

        return $status;
    }

    /**
     * @param CancelRequestDTO $dto
     * @return object { request: App\Models\Request, driverId: int|null }
     * @throws CustomErrorException
     */
    public function cancelRequest(CancelRequestDTO $dto): object
    {
        $status = $this->lookupRepository->findByCodeWhereInAndType([
            StatusPackageRequestLookup::code(StatusPackageRequestLookup::NEW),
            StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED),
        ], TypeLookup::STATUS_PACKAGE_REQUEST);

        $request = $this->requestRepository->findById($dto->request_id);
        if (!in_array($request->status_id, $status->pluck('id')->toArray())) {
            throw new CustomErrorException('La solicitud debe estar en estatus '
                .StatusPackageRequestLookup::NEW.' o '
                .StatusPackageRequestLookup::APPROVED,
                HttpCodes::HTTP_BAD_REQUEST);
        }

        $cancelStatusId = $this->lookupRepository
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::CANCELLED),
                TypeLookup::STATUS_PACKAGE_REQUEST)
            ->id;

        $requestDTO = new RequestDTO(['status_id' => $cancelStatusId]);

        if (config('app.enable_google_calendar', false) && !is_null($request->event_google_calendar_id)) {
            $this->calendarService->deleteEvent($request->event_google_calendar_id);
        }

        $lastStatusId = $request->status_id;

        $this->cancelRequestRepository->create($dto->toArray(['request_id', 'cancel_comment', 'user_id']));

        $request = $this->requestRepository->update($dto->request_id, $requestDTO->toArray(['status_id', 'event_google_calendar_id']))
                    ->fresh(['package', 'cancelRequest', 'package.driverPackageSchedule.carSchedule.car', 'user',
                            'package.driverPackageSchedule.driverSchedule.driver']);

        $statusApproved = $status->first(function (Lookup $lookup) {
            return $lookup->code === StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED);
        });

        // Si la solicitud fue aprobada anteriormente
        $driverId = null;
        if ($lastStatusId === $statusApproved->id) {

            $package = $this->packageRepository->findByRequestId($dto->request_id);
            if (!isset($package->detailExternalParcel)) {
                $emailDriver = $request->package->driverPackageSchedule->driverSchedule->driver->email;
                Mail::send(new CancelledRequestPackageInformationMail($request, $emailDriver));

                $driverId = $package->driverPackageSchedule->driverSchedule->driver_id;

                $this->driverPackageScheduleRepository->deleteByPackageId($package->id);
                $this->carScheduleRepository->delete($package->driverPackageSchedule->carSchedule->id);
                $this->driverScheduleRepository->delete($package->driverPackageSchedule->driverSchedule->id);
            }

            $this->detailExternalParcelRepository->deleteByPackageId($package->id);
        }
        
        return (object)[
            'request' => $request,
            'driverId' => $driverId
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function transferRequest(int $packageId, PackageDTO $dto): Package
    {
        return $this->packageRepository->update($packageId, $dto->toArray(['office_id']));
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
    public function approvedRequest(PackageDTO $dto): Package
    {
        $dto->request->status_id = $this->lookupRepository
            ->findByCodeAndType(
                StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED),
                TypeLookup::STATUS_PACKAGE_REQUEST
            )
            ->id;

        if (!isset($dto->detailExternalParcel)) {
            $request = $this->requestRepository->findById($dto->request_id);

            $startDate = "{$request->start_date->toDateString()} $this->START_TIME_WORKING";
            $endDate = "{$request->start_date->toDateString()} $this->END_TIME_WORKING";

            $dto->request->end_date = $endDate;
            $this->requestRepository->update($dto->request_id, $dto->request->toArray(['status_id', 'end_date']));

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

            $packageUpdate = $this->packageRepository->findById($dto->id);

            $dateGoogleCalendar = $startDate;

            $emailDriver = $packageUpdate->driverPackageSchedule->driverSchedule->driver->email;

            Mail::send(new ApprovedRequestPackageInformationMail($packageUpdate, $emailDriver));

        } else {
            $request = $this->requestRepository
                ->update($dto->request_id, $dto->request->toArray(['status_id', 'end_date']))
                ->fresh(['package', 'user']);

            $this->detailExternalParcelRepository->create($dto->detailExternalParcel->toArray([
                'package_id', 'company_name', 'tracking_code', 'url_tracking', 'weight', 'cost'
            ]));

            $packageUpdate = $this->packageRepository->findById($dto->id);
            $dateGoogleCalendar = $request->end_date;
        }

        if (config('app.enable_google_calendar', false)) {
            $emails = $this->getRecepcionistEmails($request->package->office_id);

            $event = $this->calendarService->createEventAllDay($request->title, Carbon::make($dateGoogleCalendar), $emails);

            $dto = new RequestDTO([
                'event_google_calendar_id' => $event->id
            ]);
            $this->requestRepository->update($request->id, $dto->toArray(['event_google_calendar_id']));
        }

        return $packageUpdate;
    }

    /**
     * @throws CustomErrorException
     */
    public function insertScore(ScoreDTO $score): void
    {
        $package = $this->packageRepository->findByRequestId($score->request_id);
        $typeStatusId = $this->lookupRepository
            ->findByCodeAndType(TypeRequestLookup::code(TypeRequestLookup::PARCEL),
                TypeLookup::TYPE_REQUEST)
            ->id;

        if ($package->request->type_id !== $typeStatusId) {
            throw new CustomErrorException("El tipo de solicitud debe ser de paquetería", HttpCodes::HTTP_BAD_REQUEST);
        }

        $statusPackageId = $this->lookupRepository
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::DELIVERED),
                TypeLookup::STATUS_PACKAGE_REQUEST)
            ->id;
        if ($package->request->status_id !== $statusPackageId) {
            throw new CustomErrorException("El estatus de la solicitud debe estar ".StatusPackageRequestLookup::DELIVERED,
                HttpCodes::HTTP_BAD_REQUEST);
        }

        if (is_null($package->auth_code)) {
            throw new CustomErrorException('La solicitud ya se calificó',HttpCodes::HTTP_BAD_REQUEST);
        }

        $this->packageRepository->update($package->id, ['auth_code' => null]);
        $this->scoreRepository->create($score->toArray(['request_id', 'score', 'comment']));
    }

    /**
     * @throws CustomErrorException
     */
    public function isPackageCompleted(int $requestPackageId): bool
    {
        $typePackageRequestId = $this->lookupRepository
            ->findByCodeAndType(TypeRequestLookup::code(TypeRequestLookup::PARCEL),TypeLookup::TYPE_REQUEST)
            ->id;

        $packageRequestData = $this->requestRepository->findById($requestPackageId);

        if ($packageRequestData->type_id !== $typePackageRequestId) {
            throw new ModelNotFoundException();
        }

        $statusPackageDeliveredId = $this->lookupRepository
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::DELIVERED),
                TypeLookup::STATUS_PACKAGE_REQUEST)->id;

        if ($packageRequestData->status_id !== $statusPackageDeliveredId) {
            throw new CustomErrorException('La solicitud no se encuentra habilitada.', HttpCodes::HTTP_NOT_FOUND);
        }

        return $statusPackageDeliveredId === $packageRequestData->status_id;
    }

    public function isAuthPackage(string $authCodePackage): bool
    {
        $findAuthCodePackage = $this->packageRepository->findByAuthCode($authCodePackage);
        return !is_null($findAuthCodePackage);
    }

    public function findByRequestId(int $requestId): Package
    {
        return $this->packageRepository->findByRequestId($requestId);
    }

    /**
     * @throws CustomErrorException
     */
    public function onRoad(int $requestId): Request
    {
        $statusApprovedId = $this->lookupRepository
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED),
                TypeLookup::STATUS_PACKAGE_REQUEST)->id;

        $request = $this->requestRepository->findById($requestId);

        if ($request->status->id !== $statusApprovedId) {
            throw new CustomErrorException("El estatus de la solicitud debe estar ".StatusPackageRequestLookup::APPROVED,
                HttpCodes::HTTP_BAD_REQUEST);
        }

        $onReadId = $this->lookupRepository
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::ROAD),
                TypeLookup::STATUS_PACKAGE_REQUEST)->id;

        $requestDTO = new RequestDTO(['status_id' => $onReadId]);
        return $this->requestRepository->update($requestId, $requestDTO->toArray(['status_id']));
    }

    public function findAllByDateAndOffice(int $officeId, Carbon $date): Collection
    {
        return $this->packageRepository->findAllByDateAndOffice($officeId, $date);
    }

    /**
     * @throws CustomErrorException
     */
    public function proposalRequest(PackageDTO $dto): Request
    {
        $statusNewId = $this->lookupRepository->findByCodeAndType(
            StatusPackageRequestLookup::code(StatusPackageRequestLookup::NEW),
            TypeLookup::STATUS_PACKAGE_REQUEST)->id;

        $request = $this->requestRepository->findById($dto->request_id);

        if ($request->status_id !== $statusNewId) {
            throw new CustomErrorException('La solicitud debe estar en estatus '.StatusPackageRequestLookup::NEW,
                HttpCodes::HTTP_BAD_REQUEST);
        }

        $statusProposalId = $this->lookupRepository->findByCodeAndType(
            StatusPackageRequestLookup::code(StatusPackageRequestLookup::PROPOSAL),
            TypeLookup::STATUS_PACKAGE_REQUEST)->id;
        $dto->request->status_id = $statusProposalId;

        $this->proposalRequestRepository->create($dto->proposalRequest->toArray(['request_id', 'start_date', 'end_date']));

        $this->proposalPackageRepository->create($dto->proposalPackage->toArray(['package_id', 'is_driver_selected']));

        if ($dto->proposalPackage->is_driver_selected) {
            $startDate = "{$dto->proposalRequest->start_date->toDateString()} $this->START_TIME_WORKING";
            $endDate = "{$dto->proposalRequest->start_date->toDateString()} $this->END_TIME_WORKING";

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
        }

        return $this->requestRepository->update($dto->request_id, $dto->request->toArray(['status_id']))
            ->fresh(['package']);
    }

    /**
     * @throws CustomErrorException
     */
    public function responseRejectRequest(int $requestId, RequestDTO $dto): Request
    {
        if (!in_array($dto->status->code, StatusPackageRequestLookup::getAllCodes()->all())) {
            throw new CustomErrorException('No existe el estatus', HttpCodes::HTTP_NOT_FOUND);
        }

        $proposalStatusId = $this->lookupRepository->findByCodeAndType(
            StatusPackageRequestLookup::code(StatusPackageRequestLookup::PROPOSAL),
            TypeLookup::STATUS_PACKAGE_REQUEST)
            ->id;

        $package = $this->packageRepository->findByRequestId($requestId);

        if ($package->request->status_id !== $proposalStatusId) {
            throw new CustomErrorException('La solicitud debe de estar en estatus '.StatusPackageRequestLookup::PROPOSAL,
                HttpCodes::HTTP_BAD_REQUEST);
        }

        if ($dto->status->code === StatusPackageRequestLookup::code(StatusPackageRequestLookup::IN_REVIEW) &&
            $package->proposalPackage->is_driver_selected) {
            $dto->status->code = StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED);
        }

        $dto->status_id = $this->lookupRepository
            ->findByCodeAndType($dto->status->code, TypeLookup::STATUS_PACKAGE_REQUEST)
            ->id;

        if (!is_null($dto->proposal_id)) {
            $proposalData = $this->proposalRequestRepository->findById($dto->proposal_id);
            $dto->start_date = $proposalData->start_date;

            if ($package->proposalPackage->is_driver_selected) {
                $dto->end_date = "{$proposalData->start_date->toDateString()} $this->END_TIME_WORKING";

                if (config('app.enable_google_calendar', false)) {
                    if($package->request->add_google_calendar) {
                        $emails[] = $package->request->user->email;
                    }
                    $emails = array_merge($emails, $this->getRecepcionistEmails($package->office_id));

                    $event = $this->calendarService->createEventAllDay($package->request->title, $proposalData->start_date, $emails);

                    $dto = new RequestDTO([
                        'event_google_calendar_id' => $event->id
                    ]);
                    $this->requestRepository->update($package->request_id, $dto->toArray(['event_google_calendar_id']));
                }
            } else {
                $dto->end_date = $proposalData->end_date;
            }

            $columnsRequestUpdate = ['status_id', 'start_date', 'end_date'];
        } else {
            $columnsRequestUpdate = ['status_id'];

            if ($package->proposalPackage->is_driver_selected) {
                $this->driverPackageScheduleRepository->deleteByPackageId($package->id);
                $this->carScheduleRepository->delete($package->driverPackageSchedule->carSchedule->id);
                $this->driverScheduleRepository->delete($package->driverPackageSchedule->driverSchedule->id);
            }
        }

        return $this->requestRepository->update($requestId, $dto->toArray($columnsRequestUpdate))->fresh(['status']);
    }

    /**
     * @throws CustomErrorException
     */
    public function findAllByDriverIdPaginated(HttpRequest $request, User $user, array $columns = ['*']):
        LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->requestPackageViewRepository->findAllByDriverIdPaginated($filters, $perPage, $user, $sort);
    }

    public function findAllDeliveredByDriverIdPaginated(HttpRequest $request, User $user, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->requestPackageViewRepository->findAllDeliveredByDriverIdPaginated($filters, $perPage, $user, $sort);
    }

    /**
     * @throws CustomErrorException
     */
    public function deliveredPackage(DeliveredPackageDTO $dto): Request
    {
        $package = $this->packageRepository->findById($dto->package_id);

        $statusPackageId = $this->lookupRepository
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::ROAD),
                TypeLookup::STATUS_PACKAGE_REQUEST)
            ->id;
        if ($package->request->status_id !== $statusPackageId) {
            throw new CustomErrorException("El estatus de la solicitud debe estar ".StatusPackageRequestLookup::ROAD,
                HttpCodes::HTTP_BAD_REQUEST);
        }

        $statusId = $this->lookupRepository
            ->findByCodeAndType(StatusPackageRequestLookup::code(StatusPackageRequestLookup::DELIVERED),
                TypeLookup::STATUS_PACKAGE_REQUEST)
            ->id;
        $requestDTO = new RequestDTO(['status_id' => $statusId, 'end_date' => now()]);
        $request = $this->requestRepository->update($package->request_id, $requestDTO->toArray(['status_id', 'end_date']));

        $this->deliveredPackageRepository->create($dto->toArray(['package_id', 'name_receive', 'observations']));

        $codePackage = Str::random(40);
        $packageUpdate = $this->packageRepository->update($dto->package_id, ['auth_code' => $codePackage]);
        Mail::to($packageUpdate->email_receive)->send(new ApprovedPackageMail($packageUpdate, $request->code));

        return $request;
    }

    /**
     * @throws CustomErrorException
     */
    public function deliveredRequestSignature(int $packageId, DeliveredPackageDTO $dto): void
    {
        $dto->signature = File::uploadImage($dto->signature_file, Path::PACKAGE_SIGNATURES, File::SIGNATURE_HEIGHT_IMAGE);
        $this->deliveredPackageRepository->update($packageId, $dto->toArray(['signature']));
    }

    /**
     * @throws CustomErrorException
     */
    public function reportRequestPackagePdf(HttpRequest $request, int $driverId)
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $data = $this->requestPackageViewRepository->getDataReport($filters, $driverId);
        $path = Path::STORAGE.Path::PACKAGE_SIGNATURES;

        return File::generatePDF (
            'pdf.reports.driver-delivered',
            $data,
            'solicitudes_paqueteria_entregadas',
            array('path' => $path),
            true
        );
    }

    /**
     * @throws CustomErrorException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     * @throws InvalidArgumentException
     * @throws IOException
     */
    public function reportRequestPackageExcel(HttpRequest $request, int $driverId): StreamedResponse
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $data = $this->requestPackageViewRepository->getDataReport($filters, $driverId)->map(function ($item) {
            return collect([
                'Clave' => $item->code,
                'Fecha entrega' => $item->end_date->format('d-m-Y, h:i a'),
                'Lugar salida' => $item->state_pickup,
                'Lugar llegada' => $item->state_arrival,
                'Recibió' => $item->name_receive,
            ]);
        });

        return File::generateExcel($data,'solicitudes_paqueteria_entregadas');
    }

    /**
     * @throws CustomErrorException
     */
    public function acceptCancelPackage(int $requestId, RequestDTO $dto): Request
    {
        if (!in_array($dto->status->code, StatusPackageRequestLookup::getAllCodes()->all())) {
            throw new CustomErrorException('No existe el estatus', HttpCodes::HTTP_NOT_FOUND);
        }

        $statusCode = ($dto->status->code === StatusPackageRequestLookup::code(StatusPackageRequestLookup::ACCEPT))
            ? StatusPackageRequestLookup::code(StatusPackageRequestLookup::NEW)
            : StatusPackageRequestLookup::code(StatusPackageRequestLookup::CANCELLED);

        $dto->status_id = $this->lookupRepository
            ->findByCodeAndType($statusCode,TypeLookup::STATUS_PACKAGE_REQUEST)
            ->id;

        if ($statusCode === StatusPackageRequestLookup::code(StatusPackageRequestLookup::CANCELLED)) {
            $dto->cancelRequest->request_id = $requestId;
            $this->cancelRequestRepository->create($dto->cancelRequest->toArray(['request_id', 'cancel_comment', 'user_id']));
        }

        return $this->requestRepository->update($requestId, $dto->toArray(['status_id']))
            ->fresh(['status', 'package']);
    }

    /**
     * @throws CustomErrorException
     */
    public function findAllPackagesByManagerIdPaginated(HttpRequest $request, int $departmentManagerId, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->requestPackageViewRepository->findAllPackagesByManagerIdPaginated($filters, $perPage, $departmentManagerId, $sort);
    }

    private function getRecepcionistEmails(int $officeId): array
    {
        return $this->userService->getRecepcionistByPermission($officeId, PathRouteRecepcionist::fullPathHistory(PathRouteRecepcionist::PARCEL))
            ->pluck('email')
            ->toArray();
    }
}