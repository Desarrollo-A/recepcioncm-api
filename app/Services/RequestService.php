<?php

namespace App\Services;

use App\Contracts\Repositories\AddressRepositoryInterface;
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

    public function __construct(RequestRepositoryInterface $requestRepository,
                                LookupRepositoryInterface $lookupRepository,
                                NotificationRepositoryInterface $notificationRepository,
                                ProposalRequestRepositoryInterface $proposalRequestRepository,
                                PackageRepositoryInterface $packageRepository,
                                AddressRepositoryInterface $addressRepository,
                                RequestDriverRepositoryInterface $requestDriverRepository)
    {
        $this->entityRepository = $requestRepository;
        $this->lookupRepository = $lookupRepository;
        $this->notificationRepository = $notificationRepository;
        $this->proposalRequestRepository = $proposalRequestRepository;
        $this->packageRepository = $packageRepository;
        $this->addressRepository = $addressRepository;
        $this->requestDriverRepository = $requestDriverRepository;
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
            ->getAllApprovedCarDriverRoom(['requests.id','t.code AS type_code']);
                                            
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

    public function changeToExpired()
    {
        $expired = $this->entityRepository->getExpired(['requests.id']);
        $statusId = $this->lookupRepository->findByCodeAndType(StatusRoomRequestLookup::code(StatusRoomRequestLookup::EXPIRED),
            TypeLookup::STATUS_ROOM_REQUEST)->id;
        if ($expired->count() > 0) {
            $this->entityRepository->bulkStatusUpdate(array_values($expired->toArray()), $statusId);
        }

        $proposalRequests = $this->entityRepository
            ->getPreviouslyByCode(StatusRoomRequestLookup::code(StatusRoomRequestLookup::PROPOSAL), ['requests.id']);
        if ($proposalRequests->count() > 0) {
            $this->proposalRequestRepository->deleteInRequestIds(array_values($proposalRequests->toArray()));
            $this->entityRepository->bulkStatusUpdate(array_values($proposalRequests->toArray()), $statusId);
        }
    }

    public function deleteRequestPackage(int $requestId, int $userId): Package
    {
        $package = $this->packageRepository->findByRequestId($requestId);

        if($package->request->user_id !== $userId){
            throw new AuthorizationException();
        }

        if(!is_null($package->authorization_filename)){
            File::deleteFile($package->authorization_filename, Path::PACKAGE_AUTHORIZATION_DOCUMENTS);
        }

        $this->entityRepository->delete($requestId);
        $this->addressRepository->bulkDelete([$package->pickup_address_id, $package->arrival_address_id]);
        return $package;
    }

    public function deleteRequestDriver(int $requestId, int $userId): RequestDriver
    {
        $requestDriver = $this->requestDriverRepository->findByRequestId($requestId);
        
        if ($requestDriver->request->user_id !== $userId){
            throw new AuthorizationException();
        }

        if(!is_null($requestDriver->authorization_filename)){
            File::deleteFile($requestDriver->authorization_filename, Path::DRIVER_AUTHORIZATION_DOCUMENTS);
        }

        $this->entityRepository->delete($requestId);
        $this->addressRepository->bulkDelete([$requestDriver->pickup_address_id, $requestDriver->arrival_address_id]);
        return $requestDriver;
    }
}