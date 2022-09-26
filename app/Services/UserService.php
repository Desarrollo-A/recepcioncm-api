<?php

namespace App\Services;

use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\OfficeRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\QueryParam;
use App\Helpers\Validation;
use App\Models\Dto\UserDTO;
use App\Models\Enums\Lookups\StatusUserLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService extends BaseService implements UserServiceInterface
{
    protected $entityRepository;
    protected $roleRepository;
    protected $lookupRepository;
    protected $officeRepository;

    public function __construct(UserRepositoryInterface $userRepository,
                                RoleRepositoryInterface $roleRepository,
                                LookupRepositoryInterface $lookupRepository,
                                OfficeRepositoryInterface $officeRepository)
    {
        $this->entityRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->lookupRepository = $lookupRepository;
        $this->officeRepository = $officeRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function create(UserDTO $dto): User
    {
        $dto->status_id = $this->lookupRepository->findByCodeAndType(StatusUserLookup::code(StatusUserLookup::ACTIVE),
            TypeLookup::STATUS_USER)->id;
        if ($dto->role->name === NameRole::RECEPCIONIST) {
            $dto->role_id = $this->roleRepository->findByName(NameRole::RECEPCIONIST)->id;
        } else {
            $dto->role_id = $this->roleRepository->findByName(NameRole::APPLICANT)->id;
        }
        $dto->office_id = $this->officeRepository->findByName($dto->office->name)->id;

        $data = $dto->toArray(['no_employee', 'full_name', 'email', 'password', 'personal_phone', 'office_phone',
            'position', 'area', 'status_id', 'role_id', 'office_id']);

        $user = $this->entityRepository->create($data);
        return $this->entityRepository->findById($user->id);
    }

    /**
     * @throws CustomErrorException
     */
    public function findAllPaginatedWithoutUser(Request $request, int $userId, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->entityRepository->findAllPaginatedWithoutUser($userId, $filters, $perPage, $sort, $columns);
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function changeStatus(int $id, UserDTO $dto)
    {
        $this->entityRepository->update($id, $dto->toArray(['status_id']));
    }
}