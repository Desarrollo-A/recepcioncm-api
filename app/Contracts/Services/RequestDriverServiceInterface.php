<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\CancelRequestDTO;
use App\Models\Dto\RequestDriverDTO;
use App\Models\Dto\RequestDTO;
use App\Models\Request;
use App\Models\RequestDriver;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Pagination\LengthAwarePaginator;

interface RequestDriverServiceInterface extends BaseServiceInterface
{
    public function create(RequestDriverDTO $dto): RequestDriver;

    /**
     * @param User|Authenticatable $user
     */
    public function findAllDriversPaginated(HttpRequest $request, User $user, array $columns = ['*']): LengthAwarePaginator;

    public function getStatusByStatusCurrent(string $code, string $roleName): Collection;

    public function cancelRequest(CancelRequestDTO $dto): object;

    public function transferRequest(int $requestDriverId, RequestDriverDTO $dto): RequestDriver;

    /**
     * @param User|Authenticatable $user
     */
    public function findByDriverRequestId(int $requestId, User $user): RequestDriver;

    public function approvedRequest(RequestDriverDTO $dto): Request;

    /**
     * @param User|Authenticatable $user
     */
    public function findAllByDriverIdPaginated(HttpRequest $request, User $user, array $columns = ['*']):
        LengthAwarePaginator;

    public function getBusyDaysForProposalCalendar(): array;

    public function proposalRequest(RequestDriverDTO $dto): Request;

    public function responseRejectRequest(int $requestId, RequestDTO $dto): Request;
}