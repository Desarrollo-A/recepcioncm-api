<?php

namespace App\Services;

use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Repositories\ProposalRequestRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Services\RequestServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Message;
use App\Models\Dto\RequestDTO;
use App\Models\Enums\Lookups\StatusRoomRequestLookup;
use App\Models\Enums\TypeLookup;
use App\Models\Request;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

class RequestService extends BaseService implements RequestServiceInterface
{
    protected $entityRepository;
    protected $lookupRepository;
    protected $notificationRepository;
    protected $proposalRequestRepository;

    public function __construct(RequestRepositoryInterface $requestRepository,
                                LookupRepositoryInterface $lookupRepository,
                                NotificationRepositoryInterface $notificationRepository,
                                ProposalRequestRepositoryInterface $proposalRequestRepository)
    {
        $this->entityRepository = $requestRepository;
        $this->lookupRepository = $lookupRepository;
        $this->notificationRepository = $notificationRepository;
        $this->proposalRequestRepository = $proposalRequestRepository;
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
     * @throws CustomErrorException
     */
    public function responseRejectRequest(int $id, RequestDTO $dto): Request
    {
        $proposalStatusId = $this->lookupRepository->findByCodeAndType(StatusRoomRequestLookup::code(StatusRoomRequestLookup::PROPOSAL),
            TypeLookup::STATUS_ROOM_REQUEST)->id;
        $request = $this->entityRepository->findById($id);

        if ($request->status_id !== $proposalStatusId) {
            throw new CustomErrorException('La solicitud debe de estar en estatus '.StatusRoomRequestLookup::PROPOSAL,
                Response::HTTP_BAD_REQUEST);
        }

        $dto->status_id = $this->lookupRepository->findByCodeAndType($dto->status->code, TypeLookup::STATUS_ROOM_REQUEST)->id;

        $this->proposalRequestRepository->deleteByRequestId($id);

        $data = ($dto->status->code === StatusRoomRequestLookup::code(StatusRoomRequestLookup::IN_REVIEW))
            ? $dto->toArray(['status_id', 'start_date', 'end_date'])
            : $dto->toArray(['status_id']);

        return $this->entityRepository->update($id, $data)
            ->fresh(['requestRoom', 'requestRoom.room', 'status']);
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
            ->getPreviouslyByCode(StatusRoomRequestLookup::code(StatusRoomRequestLookup::APPROVED), ['requests.id',
                'requests.user_id', 'requests.code']);
        if ($requests->count() > 0) {
            $statusId = $this->lookupRepository
                ->findByCodeAndType(StatusRoomRequestLookup::code(StatusRoomRequestLookup::FINISHED),
                    TypeLookup::STATUS_ROOM_REQUEST)->id;
            $this->entityRepository->bulkStatusUpdate(array_values($requests->pluck('id')->toArray()), $statusId);
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
}