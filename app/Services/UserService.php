<?php

namespace App\Services;

use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\OfficeRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\MenuServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Path;
use App\Helpers\Enum\QueryParam;
use App\Helpers\File;
use App\Helpers\Utils;
use App\Helpers\Validation;
use App\Mail\User\NewDriverMail;
use App\Models\Dto\BulkLoadFileDTO;
use App\Models\Dto\OfficeDTO;
use App\Models\Dto\RoleDTO;
use App\Models\Dto\UserDTO;
use App\Models\Enums\Lookups\StatusUserLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use App\Models\User;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserService extends BaseService implements UserServiceInterface
{
    protected $entityRepository;
    protected $roleRepository;
    protected $lookupRepository;
    protected $officeRepository;

    protected $menuService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        RoleRepositoryInterface $roleRepository,
        LookupRepositoryInterface $lookupRepository,
        OfficeRepositoryInterface $officeRepository,
        MenuServiceInterface $menuService
    )
    {
        $this->entityRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->lookupRepository = $lookupRepository;
        $this->officeRepository = $officeRepository;
        $this->menuService = $menuService;
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

    public function removeOldTokens(): void
    {
        $now = now();
        $this->entityRepository->findAll([], null, ['id'])->each(function (User $user) use ($now) {
            $user->tokens->each(function ($token) use ($now) {
                $createdAt = Carbon::make($token->created_at);
                if ($createdAt->diffInDays($now) > 7) {
                    $token->delete();
                }
            });
        });
    }

    /**
     * @throws CustomErrorException
     */
    public function storeDriver(UserDTO $dto): User
    {
        $dto->status_id = $this->lookupRepository
            ->findByCodeAndType(StatusUserLookup::code(StatusUserLookup::ACTIVE),TypeLookup::STATUS_USER)
            ->id;
        $dto->role_id = $this->roleRepository->findByName(NameRole::DRIVER)->id;
        $dto->office_id = $this->officeRepository->findByName($dto->office->name)->id;

        $password = Str::random();
        $dto->password = bcrypt($password);

        $data = $dto->toArray(['no_employee', 'full_name', 'email', 'password', 'personal_phone', 'office_phone',
            'position', 'area', 'status_id', 'role_id', 'office_id']);

        $user = $this->entityRepository->create($data);

        Mail::to($user)->send(new NewDriverMail($user, $password));

        return $user;
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $id, UserDTO $dto): User
    {
        return $this->entityRepository->update($id, $dto->toArray(['no_employee', 'full_name', 'email',
            'personal_phone', 'office_phone', 'position', 'area', 'office_id', 'status_id']));
    }

    /**
     * @return StreamedResponse | bool
     * @throws CustomErrorException
     * @throws ReaderNotOpenedException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function bulkStoreDriver(BulkLoadFileDTO $dto)
    {
        $filename = File::uploadFile($dto->file, Path::TMP);
        $data = (new FastExcel())->import(File::getExposedPath($filename, Path::TMP));
        File::deleteFile($filename, Path::TMP);

        if ($data->count() === 0) {
            throw new CustomErrorException('No hay registros en el archivo', Response::HTTP_BAD_REQUEST);
        }

        $errors = [];
        $usersDTO = [];
        $userArray = array_map(function ($row) {
            return [
                'no_employee' => trim($row['num_colaborador']),
                'full_name' => trim($row['nombre_completo']),
                'email' => trim($row['correo']),
                'personal_phone' => trim($row['tel_personal']),
                'office_phone' => isset($row['tel_oficina']) ? trim($row['tel_oficina']) : $row['tel_oficina'],
                'position' => trim($row['posicion']),
                'area' => trim($row['area']),
                'officeName' => trim($row['nombre_oficina'])
            ];
        }, $data->toArray());

        foreach ($userArray as $i => $data) {
            $validator = Validator::make($data, [
                'no_employee' => ['required', 'max:50', 'unique:users,no_employee'],
                'full_name' => ['required', 'max:150'],
                'email' => ['required', 'email:dns', 'max:150', 'unique:users,email'],
                'personal_phone' => ['required', 'min:10', 'max:10'],
                'office_phone' => ['nullable', 'min:10', 'max:10'],
                'position' => ['required', 'max:100'],
                'area' => ['required', 'max:100'],
                'officeName' => ['required', 'min:3', 'max:150']
            ]);

            if ($validator->fails()) {
                $errors[$i + 2] = Utils::convertErrorMessageToStringArray($validator->errors()->toArray());
                continue;
            }

            $office = new OfficeDTO(['name' => $data['officeName']]);
            $role = new RoleDTO(['name' => NameRole::DRIVER]);

            $usersDTO[] = new UserDTO([
                'no_employee' => $data['no_employee'],
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'personal_phone' => $data['personal_phone'],
                'office_phone' => $data['office_phone'],
                'position' => $data['position'],
                'area' => $data['area'],
                'role' => $role,
                'office' => $office
            ]);
        }

        if (count($errors) > 0) {
            return File::generateExcel(Utils::convertErrorMessageToCollectionExcel($errors), 'errores-archivo');
        }

        $offices = $this->officeRepository->findAllActive();
        foreach ($usersDTO as $i => $userDTO) {
            $exist = $offices
                ->where('name', '=', $userDTO->office->name)
                ->first();

            if (is_null($exist)) {
                $errors[$i + 2] = ['No existe la oficina'];
            }
        }

        if (count($errors) > 0) {
            return File::generateExcel(Utils::convertErrorMessageToCollectionExcel($errors), 'errores-archivo');
        }

        $statusId = $this->lookupRepository
            ->findByCodeAndType(StatusUserLookup::code(StatusUserLookup::ACTIVE),TypeLookup::STATUS_USER)
            ->id;
        $roleId = $this->roleRepository
            ->findByName(NameRole::DRIVER)
            ->id;

        foreach ($usersDTO as $userDTO) {
            $password = Str::random();
            $userDTO->password = bcrypt($password);
            $userDTO->status_id = $statusId;
            $userDTO->role_id = $roleId;

            $user = $this->entityRepository->create($userDTO->toArray([
                'no_employee', 'full_name', 'email', 'password', 'personal_phone',
                'office_phone', 'position', 'area', 'status_id', 'role_id', 'office_id'
            ]));

            $this->menuService->createDefaultMenu($user->id, NameRole::DRIVER);

            Mail::to($user)->send(new NewDriverMail($user, $password));
        }

        return true;
    }

    /**
     * @throws CustomErrorException
     */
    public function downUser(string $noEmployee): void
    {
        try {
            $user = $this->entityRepository->findByNoEmployee($noEmployee);
            $status = $this->lookupRepository->findByCodeAndType(
                StatusUserLookup::code(StatusUserLookup::INACTIVE), TypeLookup::STATUS_USER
            );
            $dto = new UserDTO(['status_id' => $status->id]);

            $this->entityRepository->update($user->id, $dto->toArray(['status_id']));

            $user->tokens->each(function ($token) {
                $token->delete();
            });
        } catch (ModelNotFoundException $ex) {
            //
        }
    }
}
