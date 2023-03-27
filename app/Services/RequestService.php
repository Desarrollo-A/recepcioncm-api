<?php

namespace App\Services;

use App\Contracts\Repositories\AddressRepositoryInterface;
use App\Contracts\Repositories\CarRequestScheduleRepositoryInterface;
use App\Contracts\Repositories\CarScheduleRepositoryInterface;
use App\Contracts\Repositories\DriverRequestScheduleRepositoryInterface;
use App\Contracts\Repositories\DriverScheduleRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Repositories\PackageRepositoryInterface;
use App\Contracts\Repositories\ProposalRequestRepositoryInterface;
use App\Contracts\Repositories\RequestDriverRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Services\RequestServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Message;
use App\Helpers\Enum\Path;
use App\Helpers\File;
use App\Models\Dto\RequestDTO;
use App\Models\Enums\Lookups\StatusCarRequestLookup;
use App\Models\Enums\Lookups\StatusDriverRequestLookup;
use App\Models\Enums\Lookups\StatusPackageRequestLookup;
use App\Models\Enums\Lookups\StatusRoomRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Enums\TypeLookup;
use App\Models\Package;
use App\Models\Request;
use App\Models\RequestDriver;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

class RequestService extends BaseService implements RequestServiceInterface
{
    protected $entityRepository;
    protected $lookupRepository;
    protected $notificationRepository;
    protected $proposalRequestRepository;
    protected $packageRepository;
    protected $addressRepository;
    protected $requestDriverRepository;
    protected $driverRequestScheduleRepository;
    protected $carRequestScheduleRepository;
    protected $driverScheduleRepository;
    protected $carScheduleRepository;

    public function __construct(RequestRepositoryInterface $requestRepository,
                                LookupRepositoryInterface $lookupRepository,
                                NotificationRepositoryInterface $notificationRepository,
                                ProposalRequestRepositoryInterface $proposalRequestRepository,
                                PackageRepositoryInterface $packageRepository,
                                AddressRepositoryInterface $addressRepository,
                                RequestDriverRepositoryInterface $requestDriverRepository,
                                DriverRequestScheduleRepositoryInterface $driverRequestScheduleRepository,
                                CarRequestScheduleRepositoryInterface $carRequestScheduleRepository,
                                DriverScheduleRepositoryInterface $driverScheduleRepository,
                                CarScheduleRepositoryInterface $carScheduleRepository)
    {
        $this->entityRepository = $requestRepository;
        $this->lookupRepository = $lookupRepository;
        $this->notificationRepository = $notificationRepository;
        $this->proposalRequestRepository = $proposalRequestRepository;
        $this->packageRepository = $packageRepository;
        $this->addressRepository = $addressRepository;
        $this->requestDriverRepository = $requestDriverRepository;
        $this->driverRequestScheduleRepository = $driverRequestScheduleRepository;
        $this->carRequestScheduleRepository = $carRequestScheduleRepository;
        $this->driverScheduleRepository = $driverScheduleRepository;
        $this->carScheduleRepository = $carScheduleRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function deleteRequestRoom(int $requestId, int $userId): Request
    {
        $newStatusId = $this->lookupRepository->findByCodeAndType(StatusRoomRequestLookup::code(StatusRoomRequestLookup::NEW),
            TypeLookup::STATUS_ROOM_REQUEST)->id;
        $request = $this->entityRepository->findById($requestId)->fresh(['requestRoom', 'requestRoom.room']);

        if ($request->status_id !== $newStatusId) {
            throw new CustomErrorException('La solicitud debe de estar en estatus '.StatusRoomRequestLookup::code(StatusRoomRequestLookup::NEW),
                Response::HTTP_BAD_REQUEST);
        }

        if ($request->user_id !== $userId) {
            throw new CustomErrorException(Message::AUTHORIZATION_EXCEPTION, Response::HTTP_FORBIDDEN);
        }

        $this->entityRepository->delete($requestId);

        return $request;
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function updateCode(Request $request)
    {
        $requestDTO = new RequestDTO(['code' => Request::INITIAL_CODE.$request->id]);
        $this->entityRepository->update($request->id, $requestDTO->toArray(['code']));
    }

    public function changeToFinished(): Collection
    {
        $requests = $this->entityRepository
            ->getAllApprovedCarDriverRoom(['requests.id','t.code AS type_code', 'requests.user_id', 'requests.code']);
                                            
        $filteredRoomRequests = $requests->filter(function($value){
            return $value->type_code === TypeRequestLookup::code(TypeRequestLookup::ROOM);
        });

        $filteredDriverRequests = $requests->filter(function($value){
            return $value->type_code === TypeRequestLookup::code(TypeRequestLookup::DRIVER);
        });

        $filteredCarRequests = $requests->filter(function($value){
            return $value->type_code === TypeRequestLookup::code(TypeRequestLookup::CAR);
        });

        if ($filteredRoomRequests->count() > 0) {
            $statusId = $this->lookupRepository
                ->findByCodeAndType(StatusRoomRequestLookup::code(StatusRoomRequestLookup::FINISHED),
                    TypeLookup::STATUS_ROOM_REQUEST)->id;
            $this->entityRepository->bulkStatusUpdate($filteredRoomRequests->pluck('id')->values()->toArray(), $statusId);
        }

        if ($filteredDriverRequests->count() > 0) {
            $statusId = $this->lookupRepository
                ->findByCodeAndType(StatusDriverRequestLookup::code(StatusDriverRequestLookup::FINISHED),
                    TypeLookup::STATUS_DRIVER_REQUEST)->id;
            $this->entityRepository->bulkStatusUpdate($filteredDriverRequests->pluck('id')->values()->toArray(), $statusId);
        }
        
        if ($filteredCarRequests->count() > 0) {
            $statusId = $this->lookupRepository
                ->findByCodeAndType(StatusCarRequestLookup::code(StatusCarRequestLookup::FINISHED),
                    TypeLookup::STATUS_CAR_REQUEST)->id;
            $this->entityRepository->bulkStatusUpdate($filteredCarRequests->pluck('id')->values()->toArray(), $statusId);
        }

        return $requests;
    }

    public function changeToExpired(): void
    {
        $proposalIds = [];
        $carSchedulesIds = [];
        $expired = $this->entityRepository->getExpired();

        $expiredRequestRoom = $expired->filter(function ($request) {
            return $request->type_code === TypeRequestLookup::code(TypeRequestLookup::ROOM);
        });

        if ($expiredRequestRoom->count() > 0) {
            $statusId = $this->lookupRepository->findByCodeAndType(
                StatusRoomRequestLookup::code(StatusRoomRequestLookup::EXPIRED),
                TypeLookup::STATUS_ROOM_REQUEST)
                ->id;
            $this->entityRepository->bulkStatusUpdate($expiredRequestRoom->pluck('id')->values()->toArray(), $statusId);

            $proposalRequestRoom = $expiredRequestRoom->filter(function ($request) {
                return $request->status_code === StatusRoomRequestLookup::code(StatusRoomRequestLookup::PROPOSAL);
            });

            if ($proposalRequestRoom->count() > 0) {
                $proposalIds = array_merge($proposalIds, $proposalRequestRoom->pluck('id')->values()->toArray());
            }
        }

        $expiredRequestPackage = $expired->filter(function ($request) {
            return $request->type_code === TypeRequestLookup::code(TypeRequestLookup::PARCEL);
        });

        if ($expiredRequestPackage->count() > 0) {
            $statusId = $this->lookupRepository->findByCodeAndType(
                StatusPackageRequestLookup::code(StatusPackageRequestLookup::EXPIRED),
                TypeLookup::STATUS_PACKAGE_REQUEST)
                ->id;
            $this->entityRepository->bulkStatusUpdate($expiredRequestPackage->pluck('id')->values()->toArray(), $statusId);

            $proposalRequestPackage = $expiredRequestPackage->filter(function ($request) {
                return $request->status_code === StatusPackageRequestLookup::code(StatusPackageRequestLookup::PROPOSAL);
            });

            if ($proposalRequestPackage->count() > 0) {
                $proposalIds = array_merge($proposalIds, $proposalRequestPackage->pluck('id')->values()->toArray());
            }
        }

        $expiredRequestDriver = $expired->filter(function ($request) {
            return $request->type_code === TypeRequestLookup::code(TypeRequestLookup::DRIVER);
        });

        if ($expiredRequestDriver->count() > 0) {
            $statusId = $this->lookupRepository->findByCodeAndType(
                StatusDriverRequestLookup::code(StatusDriverRequestLookup::EXPIRED),
                TypeLookup::STATUS_DRIVER_REQUEST)
                ->id;
            $this->entityRepository->bulkStatusUpdate($expiredRequestDriver->pluck('id')->values()->toArray(), $statusId);

            $proposalRequestDriver = $expiredRequestDriver->filter(function ($request) {
                return $request->status_code === StatusDriverRequestLookup::code(StatusDriverRequestLookup::PROPOSAL);
            });

            if ($proposalRequestDriver->count() > 0) {
                $proposalIds = array_merge($proposalIds, $proposalRequestDriver->pluck('id')->values()->toArray());
            }
        }

        $expiredRequestCar = $expired->filter(function ($request) {
            return $request->type_code === TypeRequestLookup::code(TypeRequestLookup::CAR);
        });

        if ($expiredRequestCar->count() > 0) {
            $statusId = $this->lookupRepository->findByCodeAndType(
                StatusCarRequestLookup::code(StatusCarRequestLookup::EXPIRED),
                TypeLookup::STATUS_CAR_REQUEST)
                ->id;
            $this->entityRepository->bulkStatusUpdate($expiredRequestCar->pluck('id')->values()->toArray(), $statusId);

            $proposalRequestCar = $expiredRequestCar->filter(function ($request) {
                return $request->status_code === StatusCarRequestLookup::code(StatusCarRequestLookup::PROPOSAL);
            });

            if ($proposalRequestCar->count() > 0) {
                $proposalIds = array_merge($proposalIds, $proposalRequestCar->pluck('id')->values()->toArray());
            }
        }

        $assignRequestDriver = $expired->filter(function ($request) {
            return !is_null($request->request_driver_id);
        });

        if ($assignRequestDriver->count() > 0) {
            $this->driverRequestScheduleRepository->bulkDeleteByRequestDriverId(
                $assignRequestDriver->pluck('request_driver_id')->values()->toArray()
            );

            $this->driverScheduleRepository->bulkDelete(
                $assignRequestDriver->pluck('drs_driver_schedule_id')->values()->toArray()
            );

            $carSchedulesIds = array_merge($carSchedulesIds,
                $assignRequestDriver->pluck('drs_car_schedule_id')->values()->toArray());
        }

        $assignRequestCar = $expired->filter(function ($request) {
            return !is_null($request->request_car_id);
        });

        if ($assignRequestCar->count() > 0) {
            $this->carRequestScheduleRepository->bulkDeleteByRequestCarId(
                $assignRequestCar->pluck('request_car_id')->values()->toArray()
            );

            $carSchedulesIds = array_merge($carSchedulesIds,
                $assignRequestCar->pluck('crs_car_schedule_id')->values()->toArray()
            );
        }

        if (count($carSchedulesIds) > 0) {
            $this->carScheduleRepository->bulkDelete($carSchedulesIds);
        }
    }

    /**
     * @throws AuthorizationException
     */
    public function deleteRequestPackage(int $requestId, int $userId): Package
    {
        $package = $this->packageRepository->findByRequestId($requestId);

        if ($package->request->user_id !== $userId) {
            throw new AuthorizationException();
        }

        $this->entityRepository->delete($requestId);

        if (!isset($package->pickupAddress->office)) {
            $this->addressRepository->delete($package->pickup_address_id);
        }
        if (!isset($package->arrivalAddress->office)) {
            $this->addressRepository->delete($package->arrival_address_id);
        }

        return $package;
    }

    /**
     * @throws AuthorizationException
     */
    public function deleteRequestDriver(int $requestId, int $userId): RequestDriver
    {
        $requestDriver = $this->requestDriverRepository->findByRequestId($requestId);
        
        if ($requestDriver->request->user_id !== $userId){
            throw new AuthorizationException();
        }

        $this->entityRepository->delete($requestId);

        if (!isset($requestDriver->pickupAddress->office)) {
            $this->addressRepository->delete($requestDriver->pickup_address_id);;
        }
        if (!isset($requestDriver->arrivalAddress->office)) {
            $this->addressRepository->delete($requestDriver->arrival_address_id);
        }

        return $requestDriver;
    }
}