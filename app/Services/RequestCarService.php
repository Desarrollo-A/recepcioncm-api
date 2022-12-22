<?php

namespace App\Services;

use App\Contracts\Repositories\CancelRequestRepositoryInterface;
use App\Contracts\Repositories\CarRequestScheduleRepositoryInterface;
use App\Contracts\Repositories\CarScheduleRepositoryInterface;
use App\Contracts\Repositories\DriverScheduleRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\RequestCarRepositoryInterface;
use App\Contracts\Repositories\RequestCarViewRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Services\CalendarServiceInterface;
use App\Contracts\Services\RequestCarServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Path;
use App\Helpers\Enum\QueryParam;
use App\Helpers\File;
use App\Helpers\Validation;
use App\Models\Dto\CancelRequestDTO;
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

    protected $calendarService;

    public function __construct(RequestCarRepositoryInterface $requestCarRepository,
                                RequestRepositoryInterface $requestRepository,
                                LookupRepositoryInterface $lookupRepository,
                                RequestCarViewRepositoryInterface $requestCarViewRepository,
                                CancelRequestRepositoryInterface $cancelRequestRepository,
                                CarScheduleRepositoryInterface $carScheduleRepository,
                                CarRequestScheduleRepositoryInterface $carRequestScheduleRepository,
                                CalendarServiceInterface $calendarService)
    {
        $this->entityRepository = $requestCarRepository;
        $this->requestRepository = $requestRepository;
        $this->lookupRepository = $lookupRepository;
        $this->requestCarViewRepository = $requestCarViewRepository;
        $this->cancelRequestRepository = $cancelRequestRepository;
        $this->carScheduleRepository = $carScheduleRepository;
        $this->carRequestScheduleRepository = $carRequestScheduleRepository;
        $this->calendarService = $calendarService;
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

        $requestCar = $this->entityRepository->create($dto->toArray(['request_id', 'office_id']));

        return $requestCar->fresh(['request']);
    }

    /**
     * @throws CustomErrorException
     */
    public function uploadAuthorizationFile(int $id, RequestCarDTO $dto): void
    {
        $dto->authorization_filename = File::uploadFile($dto->authorization_file, Path::CAR_AUTHORIZATION_DOCUMENTS);
        $this->entityRepository->update($id, $dto->toArray(['authorization_filename']));
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

    public function deleteRequestCar(int $requestId, User $user): void
    {
        $requestCar = $this->entityRepository->findByRequestId($requestId);

        if($requestCar->request->user_id !== $user->id){
            throw new AuthorizationException();
        }

        if(!is_null($requestCar->authorization_filename)){
            File::deleteFile($requestCar->authorization_filename, Path::CAR_AUTHORIZATION_DOCUMENTS);
        }

        $this->requestRepository->delete($requestId);
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
                        StatusCarRequestLookup::code(StatusCarRequestLookup::REJECTED)
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
     * @throws CustomErrorException
     */
    public function cancelRequest(CancelRequestDTO $dto): Request
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

        if (config('app.enable_google_calendar', false)) {
            $this->calendarService->deleteEvent($request->event_google_calendar_id);
        }

        $lastStatusId = $request->status_id;

        $statusApproved = $status->first(function (Lookup $lookup) {
            return $lookup->code === StatusCarRequestLookup::code(StatusCarRequestLookup::APPROVED);
        });

        // Si la solicitud fue aprobada anteriormente
        if ($lastStatusId === $statusApproved->id) {
            $requestCar = $this->entityRepository->findByRequestId($dto->request_id);
            $this->carRequestScheduleRepository->deleteByRequestCarId($requestCar->id);
            $this->carScheduleRepository->delete($requestCar->carRequestSchedule->carSchedule->id);
        }

        $request = $this->requestRepository->update($dto->request_id, $requestDTO->toArray(['status_id', 'event_google_calendar_id']));

        $this->cancelRequestRepository->create($dto->toArray(['request_id', 'cancel_comment', 'user_id']));

        return $request;
    }
}