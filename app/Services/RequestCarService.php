<?php

namespace App\Services;

use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\RequestCarRepositoryInterface;
use App\Contracts\Repositories\RequestCarViewRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Services\RequestCarServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Path;
use App\Helpers\Enum\QueryParam;
use App\Helpers\File;
use App\Helpers\Validation;
use App\Models\Dto\RequestCarDTO;
use App\Models\Enums\Lookups\StatusCarRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
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

    public function __construct(RequestCarRepositoryInterface $requestCarRepository,
                                RequestRepositoryInterface $requestRepository,
                                LookupRepositoryInterface $lookupRepository,
                                RequestCarViewRepositoryInterface $requestCarViewRepository)
    {
        $this->entityRepository = $requestCarRepository;
        $this->requestRepository = $requestRepository;
        $this->lookupRepository = $lookupRepository;
        $this->requestCarViewRepository = $requestCarViewRepository;
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
}