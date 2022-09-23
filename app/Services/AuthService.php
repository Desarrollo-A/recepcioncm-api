<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Message;
use App\Models\Enums\Lookups\StatusUserLookup;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthService extends BaseService implements AuthServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function login(string $noEmployee, string $password): Collection
    {
        $user = $this->checkAccount($noEmployee, $password);
        $token = $user->createToken('api-token')->accessToken;
        return collect($user)->put('token', $token);
    }

    public function getUser(int $id): User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * @throws CustomErrorException
     */
    private function checkAccount(string $noEmployee, string $password): User
    {
        try {
            $user = $this->userRepository->findByNoEmployee($noEmployee);
        } catch (ModelNotFoundException $ex) {
            throw new CustomErrorException(Message::CREDENTIALS_INVALID, Response::HTTP_BAD_REQUEST);
        }

        if ($user->status->code === StatusUserLookup::code(StatusUserLookup::INACTIVE)) {
            throw new CustomErrorException(Message::USER_INACTIVE, Response::HTTP_BAD_REQUEST);
        }

        if ($user->status->code === StatusUserLookup::code(StatusUserLookup::BLOCKED)) {
            throw new CustomErrorException(Message::USER_BLOCKED, Response::HTTP_BAD_REQUEST);
        }

        if (!Hash::check($password, $user->password)) {
            throw new CustomErrorException(Message::CREDENTIALS_INVALID, Response::HTTP_BAD_REQUEST);
        }

        return $user;
    }
}