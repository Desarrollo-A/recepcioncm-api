<?php

namespace App\Services;

use App\Contracts\Repositories\CancelRequestRepositoryInterface;
use App\Contracts\Repositories\CarRequestScheduleRepositoryInterface;
use App\Contracts\Repositories\CarScheduleRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\ProposalRequestRepositoryInterface;
use App\Contracts\Repositories\RequestCarRepositoryInterface;
use App\Contracts\Repositories\RequestCarViewRepositoryInterface;
use App\Contracts\Repositories\RequestEmailRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\CalendarServiceInterface;
use App\Contracts\Services\RequestCarServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Path;
use App\Helpers\Enum\QueryParam;
use App\Helpers\File;
use App\Helpers\Validation;
use App\Models\Dto\CancelRequestDTO;
use App\Models\Dto\ProposalRequestDTO;
use App\Models\Dto\RequestCarDTO;
use App\Models\Dto\RequestDTO;
use App\Models\Enums\Lookups\StatusCarRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use App\Models\Lookup;
use App\Models\Request;
use App\Models\RequestCar;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response as HttpCodes;

class RequestCarService extends BaseService implements RequestCarServiceInterface
{
    protected $entityRepository;
    protected $requestRepository;
    protected $lookupRepository;
    protected $requestCarViewRepository;
    protected $cancelRequestRepository;
    protected $carScheduleRepository;
    protected $carRequestScheduleRepository;
    protected $proposalRequestRepository;
    protected $userRepository;
    protected $requestEmailRepository;
    protected $calendarService;

    public function __construct(
        RequestCarRepositoryInterface $requestCarRepository,
        RequestRepositoryInterface $requestRepository,
        LookupRepositoryInterface $lookupRepository,
        RequestCarViewRepositoryInterface $requestCarViewRepository,
        CancelRequestRepositoryInterface $cancelRequestRepository,
        CarScheduleRepositoryInterface $carScheduleRepository,
        CarRequestScheduleRepositoryInterface $carRequestScheduleRepository,
        ProposalRequestRepositoryInterface $proposalRequestRepository,
        CalendarServiceInterface $calendarService,
        UserRepositoryInterface $userRepository,
        RequestEmailRepositoryInterface $requestEmailRepository
    )
    {
        $this->entityRepository = $requestCarRepository;
        $this->requestRepository = $requestRepository;
        $this->lookupRepository = $lookupRepository;
        $this->requestCarViewRepository = $requestCarViewRepository;
        $this->cancelRequestRepository = $cancelRequestRepository;
        $this->carScheduleRepository = $carScheduleRepository;
        $this->carRequestScheduleRepository = $carRequestScheduleRepository;
        $this->proposalRequestRepository = $proposalRequestRepository;
        $this->calendarService = $calendarService;
        $this->userRepository = $userRepository;
        $this->requestEmailRepository = $requestEmailRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function create(RequestCarDTO $dto): RequestCar
    {
        $dto->request->status_id = $this->lookupRepository
            ->findByCodeAndType(StatusCarRequestLookup::code(StatusCarRequestLookup::NEW),
                TypeLookup::STATUS_CAR_REQUEST)
            ->id;
        $dto->request->type_id = $this->lookupRepository
            ->findByCodeAndType(TypeRequestLookup::code(TypeRequestLookup::CAR),
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

        $requestCar = $this->entityRepository->create($dto->toArray(['request_id', 'office_id']));

        return $requestCar->fresh(['request']);
    }

    /**
     * @throws CustomErrorException
     */
    public function findAllCarsPaginated(HttpRequest $request, User $user, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->requestCarViewRepository->findAllRequestsCarPaginated($filters, $perPage, $user, $sort);
    }

    /**
     * @throws AuthorizationException
     */
    public function deleteRequestCar(int $requestId, User $user): RequestCar
    {
        $requestCar = $this->entityRepository->findByRequestId($requestId);

        if($requestCar->request->user_id !== $user->id){
            throw new AuthorizationException();
        }

        $this->requestRepository->delete($requestId);

        return $requestCar;
    }

    /**
     * @throws AuthorizationException
     */
    public function findByRequestId(int $requestId, User $user): RequestCar
    {
        $requestCar = $this->entityRepository->findByRequestId($requestId);

        if ($user->role->name === NameRole::RECEPCIONIST) {
            if ($user->office_id !== $requestCar->office_id){
                throw new AuthorizationException();
            }
        } else if ($user->role->name === NameRole::APPLICANT) {
            if ($user->id !== $requestCar->request->user_id) {
                throw new AuthorizationException();
            }
        }

        return $requestCar;
    }

    /**
     * @throws CustomErrorException
     */
    public function getStatusByStatusCurrent(string $code, string $roleName): Collection
    {
        if (!in_array($code, StatusCarRequestLookup::getAllCodes()->all())) {
            throw new CustomErrorException('No existe el estatus', HttpCodes::HTTP_NOT_FOUND);
        }

        $status = Collection::make();
        if ($roleName === NameRole::RECEPCIONIST) {
            switch ($code) {
                case StatusCarRequestLookup::code(StatusCarRequestLookup::NEW):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusCarRequestLookup::code(StatusCarRequestLookup::PROPOSAL),
                        StatusCarRequestLookup::code(StatusCarRequestLookup::APPROVED),
                        StatusCarRequestLookup::code(StatusCarRequestLookup::TRANSFER),
                        StatusCarRequestLookup::code(StatusCarRequestLookup::CANCELLED)
                    ], TypeLookup::STATUS_CAR_REQUEST);
                    break;
                case StatusCarRequestLookup::code(StatusCarRequestLookup::APPROVED):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusCarRequestLookup::code(StatusCarRequestLookup::CANCELLED)
                    ], TypeLookup::STATUS_CAR_REQUEST);
                    break;
            }
        } else if ($roleName === NameRole::APPLICANT) {
            switch ($code) {
                case StatusCarRequestLookup::code(StatusCarRequestLookup::PROPOSAL):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusCarRequestLookup::code(StatusCarRequestLookup::REJECTED),
                        StatusCarRequestLookup::code(StatusCarRequestLookup::ACCEPTED)
                    ], TypeLookup::STATUS_CAR_REQUEST);
                    break;
                case StatusCarRequestLookup::code(StatusCarRequestLookup::APPROVED):
                    $status = $this->lookupRepository->findByCodeWhereInAndType([
                        StatusCarRequestLookup::code(StatusCarRequestLookup::CANCELLED)
                    ], TypeLookup::STATUS_CAR_REQUEST);
                    break;
            }
        }

        return $status;
    }

    public function transferRequest(int $requestCarId, RequestCarDTO $dto): RequestCar
    {
        return $this->entityRepository->update($requestCarId, $dto->toArray(['office_id']));
    }

    /**
     * @return object {previouslyApproved: boolean, request: App\Models\Request}
     * @throws CustomErrorException
     */
    public function cancelRequest(CancelRequestDTO $dto): object
    {
        $status = $this->lookupRepository->findByCodeWhereInAndType([
            StatusCarRequestLookup::code(StatusCarRequestLookup::NEW),
            StatusCarRequestLookup::code(StatusCarRequestLookup::APPROVED),
        ], TypeLookup::STATUS_CAR_REQUEST);

        $request = $this->requestRepository->findById($dto->request_id);

        if (!in_array($request->status_id, $status->pluck('id')->toArray())) {
            throw new CustomErrorException('La solicitud debe estar en estatus '
                .StatusCarRequestLookup::code(StatusCarRequestLookup::NEW).' o '
                .StatusCarRequestLookup::code(StatusCarRequestLookup::APPROVED),
                HttpCodes::HTTP_BAD_REQUEST);
        }

        $cancelStatusId = $this->lookupRepository
            ->findByCodeAndType(StatusCarRequestLookup::code(StatusCarRequestLookup::CANCELLED),
                TypeLookup::STATUS_CAR_REQUEST)
            ->id;

        $requestDTO = new RequestDTO(['status_id' => $cancelStatusId]);

        if (config('app.enable_google_calendar', false) && !is_null($request->event_google_calendar_id)) {
            $this->calendarService->deleteEvent($request->event_google_calendar_id);
        }

        $lastStatusId = $request->status_id;

        $statusApproved = $status->first(function (Lookup $lookup) {
            return $lookup->code === StatusCarRequestLookup::code(StatusCarRequestLookup::APPROVED);
        });

        // Si la solicitud fue aprobada anteriormente
        $previouslyApproved = false;
        if ($lastStatusId === $statusApproved->id) {
            $previouslyApproved = true;
            $requestCar = $this->entityRepository->findByRequestId($dto->request_id);
            $this->carRequestScheduleRepository->deleteByRequestCarId($requestCar->id);
            $this->carScheduleRepository->delete($requestCar->carRequestSchedule->carSchedule->id);
        }

        $request = $this->requestRepository->update($dto->request_id, $requestDTO->toArray(['status_id', 'event_google_calendar_id']));

        $this->cancelRequestRepository->create($dto->toArray(['request_id', 'cancel_comment', 'user_id']));

        return (object)[
            'previouslyApproved' => $previouslyApproved,
            'request' => $request->fresh(['requestCar', 'status', 'cancelRequest'])
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function approvedRequest(RequestCarDTO $dto): Request
    {
        $dto->request->status_id = $this->lookupRepository
            ->findByCodeAndType(StatusCarRequestLookup::code(StatusCarRequestLookup::APPROVED),
                TypeLookup::STATUS_CAR_REQUEST)
            ->id;

        $request = $this->requestRepository->findById($dto->request_id);

        $dto->carRequestSchedule->carSchedule->start_date = $request->start_date;
        $dto->carRequestSchedule->carSchedule->end_date = $request->end_date;
        $carSchedule = $this->carScheduleRepository
            ->create($dto->carRequestSchedule->carSchedule->toArray(['car_id', 'start_date', 'end_date']));

        $dto->carRequestSchedule->car_schedule_id = $carSchedule->id;
        $this->carRequestScheduleRepository
            ->create($dto->carRequestSchedule->toArray(['request_car_id', 'car_schedule_id']));

        $request = $this->requestRepository->update($dto->request_id, $dto->request->toArray(['status_id']))
            ->fresh(['requestCar', 'requestCar.carRequestSchedule',
                'requestCar.carRequestSchedule.carSchedule',
                'requestCar.carRequestSchedule.carSchedule.car', 'status', 'user']);

        if (config('app.enable_google_calendar', false)) {
            if ($request->add_google_calendar) {
                $emails[] = $request->user->email;
            }

            $emails[] = $this->userRepository
                ->findByOfficeIdAndRoleRecepcionist($request->requestCar->office_id)
                ->email;
            
            $event = $this->calendarService->createEvent($request->title, $request->start_date, $request->end_date, $emails);

            $dto = new RequestDTO([
                'event_google_calendar_id' => $event->id
            ]);
            $this->requestRepository->update($request->id, $dto->toArray(['event_google_calendar_id']));
        }
    
        return $request;
    }

    public function getBusyDaysForProposalCalendar(): array
    {
        $data = $this->carRequestScheduleRepository->getBusyDaysForProposalCalendar()
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
    public function proposalRequest(RequestCarDTO $dto): Request
    {
        $dto->request->status_id = $this->lookupRepository
            ->findByCodeAndType(StatusCarRequestLookup::code(StatusCarRequestLookup::PROPOSAL),
                TypeLookup::STATUS_CAR_REQUEST)
            ->id;

        $dto->carRequestSchedule->request_car_id = $this->entityRepository->findByRequestId($dto->request_id)->id;
        $request = $this->requestRepository->update($dto->request_id, $dto->request->toArray(['status_id']));

        $carSchedule = $this->carScheduleRepository
            ->create($dto->carRequestSchedule->carSchedule->toArray(['car_id', 'start_date', 'end_date']));

        $dto->carRequestSchedule->car_schedule_id = $carSchedule->id;
        $this->carRequestScheduleRepository
            ->create($dto->carRequestSchedule->toArray(['request_car_id', 'car_schedule_id']));

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
        $proposalStatusId = $this->lookupRepository->findByCodeAndType(StatusCarRequestLookup::code(
            StatusCarRequestLookup::PROPOSAL), TypeLookup::STATUS_CAR_REQUEST)->id;
        $request = $this->requestRepository->findById($requestId);

        if ($request->status_id !== $proposalStatusId) {
            throw new CustomErrorException('La solicitud debe de estar en estatus '.StatusCarRequestLookup::PROPOSAL,
                HttpCodes::HTTP_BAD_REQUEST);
        }

        $statusCode = ($dto->status->code === StatusCarRequestLookup::code(StatusCarRequestLookup::ACCEPTED))
            ? StatusCarRequestLookup::code(StatusCarRequestLookup::APPROVED)
            : $dto->status->code;
        $dto->status = $this->lookupRepository->findByCodeAndType($statusCode, TypeLookup::STATUS_CAR_REQUEST);
        $dto->status_id = $dto->status->id;

        if ($dto->status->code === StatusCarRequestLookup::code(StatusCarRequestLookup::REJECTED)) {
            $requestCar = $this->entityRepository->findByRequestId($requestId);
            $this->carRequestScheduleRepository->deleteByRequestCarId($requestCar->id);
            $this->carScheduleRepository->delete($requestCar->carRequestSchedule->carSchedule->id);

            $request = $this->requestRepository->update($requestId, $dto->toArray(['status_id']))
                ->fresh(['status', 'requestCar']);
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

    /**
     * @throws CustomErrorException
     */
    public function uploadZipImages(int $id, RequestCarDTO $dto): void
    {
        $dto->image_zip = File::uploadFile($dto->image_zip_file, Path::REQUEST_CAR_IMAGES);
        $this->entityRepository->update($id, $dto->toArray(['image_zip']));
    }

    /**
     * @throws CustomErrorException
     */
    public function addExtraCarInformation(int $id, RequestCarDTO $dto): void
    {
        $fields = array();
        if (!is_null($dto->initial_km)) {
            $fields = array_merge($fields, ['initial_km']);
        }
        if (!is_null($dto->final_km)) {
            $fields = array_merge($fields, ['final_km']);
        }
        if (!is_null($dto->delivery_condition)) {
            $fields = array_merge($fields, ['delivery_condition']);
        }

        $this->entityRepository->update($id, $dto->toArray($fields));
    }
}
