<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\CancelRequestDTO;
use App\Models\Dto\RequestDriverDTO;
use App\Models\Request;
use App\Models\RequestDriver;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Pagination\LengthAwarePaginator;

interface RequestDriverServiceInterface extends BaseServiceInterface
{
    public function create(RequestDriverDTO $dto): RequestDriver;

    public function uploadAuthorizationFile(int $id, RequestDriverDTO $dto): void;

    public function findAllDriversPaginated(HttpRequest $request, User $user, array $columns = ['*']): LengthAwarePaginator;

    public function getStatusByStatusCurrent(string $code, string $roleName): Collection;

    public function cancelRequest(CancelRequestDTO $dto): Request;

    public function transferRequest(int $requestDriverId, RequestDriverDTO $dto): RequestDriver;

    public function findByDriverRequestId(int $requestId, User $user): RequestDriver;
}