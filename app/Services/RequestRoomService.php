<?php

namespace App\Services;

use App\Contracts\Repositories\CancelRequestRepositoryInterface;
use App\Contracts\Repositories\InventoryRepositoryInterface;
use App\Contracts\Repositories\InventoryRequestRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\ProposalRequestRepositoryInterface;
use App\Contracts\Repositories\RequestPhoneNumberRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Repositories\RequestRoomRepositoryInterface;
use App\Contracts\Repositories\RequestRoomViewRepositoryInterface;
use App\Contracts\Services\CalendarServiceInterface;
use App\Contracts\Services\RequestRoomServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Message;
use App\Helpers\Enum\QueryParam;
use App\Helpers\Utils;
use App\Helpers\Validation;
use App\Models\Dto\CancelRequestDTO;
use App\Models\Dto\RequestDTO;
use App\Models\Dto\RequestRoomDTO;
use App\Models\Enums\Lookups\InventoryTypeLookup;
use App\Models\Enums\Lookups\StatusRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use App\Models\RequestRoom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

class RequestRoomService extends BaseService implements RequestRoomServiceInterface
{
    protected $entityRepository;
    protected $requestRepository;
    protected $lookupRepository;
    protected $requestRoomViewRepository;
    protected $inventoryRepository;
    protected $inventoryRequestRepository;
    protected $cancelRequestRepository;
    protected $proposalRequestRepository;
    protected $requestPhoneNumberRepository;

    protected $calendarService;

    const DIFF_HOURS_TO_CANCEL = 1;

    public function __construct(RequestRoomRepositoryInterface $requestRoomRepository,
                                RequestRepositoryInterface $requestRepository,
                                LookupRepositoryInterface $lookupRepository,
                                RequestRoomViewRepositoryInterface $requestRoomViewRepository,
                                InventoryRepositoryInterface $inventoryRepository,
                                InventoryRequestRepositoryInterface $inventoryRequestRepository,
                                CancelRequestRepositoryInterface $cancelRequestRepository,
                                ProposalRequestRepositoryInterface $proposalRequestRepository,
                                RequestPhoneNumberRepositoryInterface $requestPhoneNumberRepository,
                                CalendarServiceInterface $calendarService)
    {
        $this->entityRepository = $requestRoomRepository;
        $this->requestRepository = $requestRepository;
        $this->lookupRepository = $lookupRepository;
        $this->requestRoomViewRepository = $requestRoomViewRepository;
        $this->inventoryRepository = $inventoryRepository;
        $this->inventoryRequestRepository = $inventoryRequestRepository;
        $this->cancelRequestRepository = $cancelRequestRepository;
        $this->proposalRequestRepository = $proposalRequestRepository;
        $this->requestPhoneNumberRepository = $requestPhoneNumberRepository;

        $this->calendarService = $calendarService;
    }

    /**
     * @throws CustomErrorException
     */
    public function findAllRoomsPaginated(Request $request, User $user, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->requestRoomViewRepository->findAllRoomsPaginated($filters, $perPage, $user, $sort, $columns);
    }

    /**
     * @throws CustomErrorException
     */
    public function create(RequestRoomDTO $dto): RequestRoom
    {
        $dto->request->status_id = $this->lookupRepository->findByCodeAndType(StatusRequestLookup::code(StatusRequestLookup::NEW),
            TypeLookup::STATUS_REQUEST)->id;
        $dto->request->type_id = $this->lookupRepository->findByCodeAndType(TypeRequestLookup::code(TypeRequestLookup::ROOM),
            TypeLookup::TYPE_REQUEST)->id;

        $request = $this->requestRepository->create($dto->request->toArray(['title', 'start_date', 'end_date', 'type_id',
            'comment', 'add_google_calendar', 'people', 'user_id', 'status_id']));

        if (count($dto->request->requestPhoneNumber) > 0) {
            $phonesInsert = array();
            foreach ($dto->request->requestPhoneNumber as $data) {
                $data->request_id = $request->id;
                $phonesInsert[] = $data->toArray(['request_id', 'name', 'phone', 'created_at', 'updated_at']);
            }
            $this->requestPhoneNumberRepository->massInsert($phonesInsert);
        }

        $dto->request_id = $request->id;
        return $this->entityRepository
            ->create($dto->toArray(['request_id', 'room_id', 'external_people','level_id', 'duration']))
            ->fresh(['request', 'room', 'level']);
    }

    public function isAvailableSchedule(Carbon $startDate, Carbon $endDate): bool
    {
        return $this->requestRepository->isAvailableSchedule($startDate, $endDate);
    }

    /**
     * @throws CustomErrorException
     */
    public function assignSnack(RequestRoomDTO $dto, int $officeId): \App\Models\Request
    {
        $newStatusId = $this->lookupRepository->findByCodeAndType(StatusRequestLookup::code(StatusRequestLookup::NEW),
            TypeLookup::STATUS_REQUEST)->id;
        $responseStatusId = $this->lookupRepository->findByCodeAndType(StatusRequestLookup::code(StatusRequestLookup::IN_REVIEW),
            TypeLookup::STATUS_REQUEST)->id;
        $request = $this->requestRepository->findById($dto->request_id);

        if ($request->status_id !== $newStatusId && $request->status_id !== $responseStatusId) {
            throw new CustomErrorException('La solicitud debe estar en estatus '. StatusRequestLookup::NEW.
                'o '.StatusRequestLookup::IN_REVIEW,
                Response::HTTP_BAD_REQUEST);
        }

        $snackTypeId = $this->lookupRepository->findByCodeAndType(InventoryTypeLookup::code(InventoryTypeLookup::COFFEE),
            TypeLookup::INVENTORY_TYPE)->id;

        $inventories = $this->inventoryRepository->findAllByType($snackTypeId, $officeId);

        self::validateInventoryAsSnack($inventories, $dto->inventoryRequest, $snackTypeId);
        self::validateStockSnack($inventories, $dto->inventoryRequest);

        foreach($dto->inventoryRequest as $snack) {
            $this->inventoryRequestRepository->create($snack->toArray(['request_id', 'inventory_id', 'quantity']));
        }

        $approveStatusId = $this->lookupRepository->findByCodeAndType(StatusRequestLookup::code(StatusRequestLookup::APPROVED),
            TypeLookup::STATUS_REQUEST)->id;
        $requestDTO = new RequestDTO(['status_id' => $approveStatusId]);

        $request = $this->requestRepository->update($dto->request_id, $requestDTO->toArray(['status_id']))
            ->fresh(['requestRoom.room.recepcionist', 'user']);

        if (config('app.enable_google_calendar', false)) {
            $emails = array();
            $emails[] = $request->requestRoom->room->recepcionist->email;
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
    public function getStatusByStatusCurrent(string $code, string $roleName): Collection
    {
        if (!in_array($code, StatusRequestLookup::getAllCodes()->all())) {
            throw new CustomErrorException('No existe el estatus', Response::HTTP_NOT_FOUND);
        }

        $status = Collection::make([]);
        if ($roleName === NameRole::RECEPCIONIST) {
            switch ($code) {
                case StatusRequestLookup::code(StatusRequestLookup::NEW):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([StatusRequestLookup::code(StatusRequestLookup::APPROVED),
                        StatusRequestLookup::code(StatusRequestLookup::PROPOSAL)],
                        TypeLookup::STATUS_REQUEST);
                    break;
                case StatusRequestLookup::code(StatusRequestLookup::APPROVED):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([StatusRequestLookup::code(StatusRequestLookup::CANCELLED),
                        StatusRequestLookup::code(StatusRequestLookup::WITHOUT_ATTENDING)], TypeLookup::STATUS_REQUEST);
                    break;
                case StatusRequestLookup::code(StatusRequestLookup::IN_REVIEW):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([StatusRequestLookup::code(StatusRequestLookup::APPROVED)],
                        TypeLookup::STATUS_REQUEST);
                    break;
            }
        } else if ($roleName === NameRole::APPLICANT) {
            switch ($code) {
                case StatusRequestLookup::code(StatusRequestLookup::APPROVED):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([StatusRequestLookup::code(StatusRequestLookup::CANCELLED)],
                        TypeLookup::STATUS_REQUEST);
                    break;
                case StatusRequestLookup::code(StatusRequestLookup::PROPOSAL):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([StatusRequestLookup::code(StatusRequestLookup::REJECTED)],
                        TypeLookup::STATUS_REQUEST);
                    break;
            }
        }

        return $status;
    }

    /**
     * @throws CustomErrorException
     */
    public function findByRequestId(int $requestId, User $user): RequestRoom
    {
        $requestRoom = $this->entityRepository->findById($requestId);
        if ($user->role->name === NameRole::RECEPCIONIST) {
            if ($user->office_id !== $requestRoom->room->office_id) {
                throw new CustomErrorException(Message::AUTHORIZATION_EXCEPTION, Response::HTTP_FORBIDDEN);
            }
        } else if ($user->role->name === NameRole::APPLICANT) {
            if ($user->id !== $requestRoom->request->user_id) {
                throw new CustomErrorException(Message::AUTHORIZATION_EXCEPTION, Response::HTTP_FORBIDDEN);
            }
        }

        return $requestRoom->append(['total_request_approved']);
    }

    /**
     * @return \App\Models\Request
     * @throws CustomErrorException
     */
    public function cancelRequest(CancelRequestDTO $dto, User $user): \App\Models\Request
    {
        $statusApproveId = $this->lookupRepository->findByCodeAndType(StatusRequestLookup::code(StatusRequestLookup::APPROVED),
            TypeLookup::STATUS_REQUEST)->id;

        $request = $this->requestRepository->findById($dto->request_id);

        if ($request->status_id !== $statusApproveId) {
            throw new CustomErrorException('La solicitud debe estar en estatus '.StatusRequestLookup::code(StatusRequestLookup::APPROVED),
                Response::HTTP_BAD_REQUEST);
        }

        if ($user->role->name === NameRole::APPLICANT) {
            $startDate = new Carbon($request->start_date);
            if (now()->diffInHours($startDate) < 1) {
                throw new CustomErrorException('No es posible cancelar la solicitud, el límite para cancelar es '.
                    self::DIFF_HOURS_TO_CANCEL.' hora antes de la reunión programada. Favor de comunicarse con recepción',
                    Response::HTTP_BAD_REQUEST);
            }
        }

        $cancelStatusId = $this->lookupRepository->findByCodeAndType(StatusRequestLookup::code(StatusRequestLookup::CANCELLED),
            TypeLookup::STATUS_REQUEST)->id;
        $requestDTO = new RequestDTO([
            'status_id' => $cancelStatusId,
            'event_google_calendar_id' => null
        ]);

        if (config('app.enable_google_calendar', false)) {
            $this->calendarService->deleteEvent($request->event_google_calendar_id);
        }

        $request = $this->requestRepository->update($dto->request_id, $requestDTO->toArray(['status_id', 'event_google_calendar_id']))
            ->fresh(['requestRoom','requestRoom.room','cancelRequest']);

        $this->cancelRequestRepository->create($dto->toArray(['request_id', 'cancel_comment', 'user_id']));

        return $request;
    }

    public function getAvailableScheduleByDay(int $requestId, Carbon $date): \Illuminate\Support\Collection
    {
        $requestsInDay = $this->requestRepository->roomsSetAsideByDay($date);
        $requestsProposalInDay = $this->proposalRequestRepository->roomsSetAsideByDay($date);
        $duration = $this->entityRepository->findById($requestId)->duration / 60;
        if ($requestsInDay->count() === 0 && $requestsProposalInDay->count() === 0) {
            return collect(Utils::getAvailableSchedule($date->format('Y-m-d'), $duration));
        }

        $schedule = Utils::getAvailableSchedule($date->format('Y-m-d'), $duration);
        foreach($schedule as $index => $time) {
            foreach ($requestsInDay as $request) {
                if ($this->isScheduleBusy($time, $request)) {
                    unset($schedule[$index]);
                    continue 2;
                }
            }

            foreach ($requestsProposalInDay as $request) {
                if ($this->isScheduleBusy($time, $request)) {
                    unset($schedule[$index]);
                    continue 2;
                }
            }
        }
        return collect(array_values($schedule));
    }

    /**
     * @throws CustomErrorException
     */
    public function proposalRequest(int $requestId, RequestDTO $dto): \App\Models\Request
    {
        $statusNewId = $this->lookupRepository->findByCodeAndType(StatusRequestLookup::code(StatusRequestLookup::NEW),
            TypeLookup::STATUS_REQUEST)->id;

        $request = $this->requestRepository->findById($requestId);

        if ($request->status_id !== $statusNewId) {
            throw new CustomErrorException('La solicitud debe estar en estatus '.StatusRequestLookup::NEW,
                Response::HTTP_BAD_REQUEST);
        }

        $statusProposalId = $this->lookupRepository->findByCodeAndType(StatusRequestLookup::code(StatusRequestLookup::PROPOSAL),
            TypeLookup::STATUS_REQUEST)->id;
        $dto->status_id = $statusProposalId;

        $request = $this->requestRepository->update($requestId, $dto->toArray(['status_id']));

        foreach ($dto->proposalRequest as $proposal) {
            $proposal->request_id = $requestId;
            $this->proposalRequestRepository->create($proposal->toArray(['request_id', 'start_date', 'end_date']));
        }

        return $request;
    }

    /**
     * @throws CustomErrorException
     */
    public function withoutAttendingRequest(int $requestId): \App\Models\Request
    {
        $statusApproveId = $this->lookupRepository->findByCodeAndType(StatusRequestLookup::code(StatusRequestLookup::APPROVED),
            TypeLookup::STATUS_REQUEST)->id;

        $request = $this->requestRepository->findById($requestId);

        if ($request->status_id !== $statusApproveId) {
            throw new CustomErrorException('La solicitud debe estar en estatus '.StatusRequestLookup::APPROVED,
                Response::HTTP_BAD_REQUEST);
        }

        $withoutAttendingStatusId = $this->lookupRepository->findByCodeAndType(StatusRequestLookup::code(StatusRequestLookup::WITHOUT_ATTENDING),
            TypeLookup::STATUS_REQUEST)->id;
        $dto = new RequestDTO(['status_id' => $withoutAttendingStatusId]);

        return $this->requestRepository->update($requestId, $dto->toArray(['status_id']));
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    private function validateStockSnack(Collection $snacks, array $inventoryRequest)
    {
        collect($inventoryRequest)->each(function ($item, $i) use ($snacks) {
            $snack = $snacks->first(function ($inventory) use ($item) {
                return $item->inventory_id === $inventory->id;
            });

            if (!is_null($snack->meeting) && !is_null($item->quantity)) {
                throw new CustomErrorException("Snack ($i) no debe tener cantidad a descontar",
                    Response::HTTP_BAD_REQUEST);
            }
            if (is_null($snack->meeting) && is_null($item->quantity)) {
                throw new CustomErrorException("Snack ($i) debe tener cantidad a descontar",
                    Response::HTTP_BAD_REQUEST);
            }
            if (($snack->stock - $item->quantity) < 0) {
                throw new CustomErrorException("Snack ($i) no debe quedar stock negativo",
                    Response::HTTP_BAD_REQUEST);
            }
        });
    }

    /**
     * @throws CustomErrorException
     * @return void
     */
    private function validateInventoryAsSnack(Collection $snacks, array $inventoryRequest, int $snackTypeId)
    {
        collect($inventoryRequest)->each(function ($item, $i) use ($snacks, $snackTypeId) {
            $snack = $snacks->first(function ($inventory) use ($item, $snackTypeId) {
                return $item->inventory_id === $inventory->id
                && $inventory->type_id === $snackTypeId;
            });

            if (is_null($snack)) {
                throw new CustomErrorException("Snack ($i) no encontrado", Response::HTTP_BAD_REQUEST);
            }
        });
    }

    private function isScheduleBusy(array $time, $request): bool
    {
        $startTime = new Carbon($request->start_date);
        $endTime = new Carbon($request->end_date);
        return (($time['start_time'] >= $startTime && $time['start_time'] < $endTime) ||
            ($time['end_time'] > $startTime && $time['end_time'] <= $endTime));
    }
}