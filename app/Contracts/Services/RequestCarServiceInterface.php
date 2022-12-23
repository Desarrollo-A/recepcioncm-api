<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\CancelRequestDTO;
use App\Models\Dto\RequestCarDTO;
use App\Models\Request;
use App\Models\RequestCar;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request as HttpRequest;

interface RequestCarServiceInterface extends BaseServiceInterface
{
    public function create(RequestCarDTO $dto): RequestCar;

    public function uploadAuthorizationFile(int $id, RequestCarDTO $dto): void;

    /**
     * @param User|Authenticatable $user
     */
    public function findAllCarsPaginated(HttpRequest $request, User $user, array $columns = ['*']): LengthAwarePaginator;

    /**
     * @param User|Authenticatable $user
     */
    public function deleteRequestCar(int $requestId, User $user): void;

    /**
     * @param User|Authenticatable $user
     */
    public function findByRequestId(int $requestId, User $user): RequestCar;

    public function getStatusByStatusCurrent(string $code, string $roleName): Collection;

    public function transferRequest(int $requestCarId, RequestCarDTO $dto): RequestCar;

    public function cancelRequest(CancelRequestDTO $dto): Request;

    public function approvedRequest(RequestCarDTO $dto): Request;
}