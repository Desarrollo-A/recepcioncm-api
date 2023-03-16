<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\CancelRequestDTO;
use App\Models\Dto\RequestCarDTO;
use App\Models\Dto\RequestDTO;
use App\Models\Request;
use App\Models\RequestCar;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Pagination\LengthAwarePaginator;

interface RequestCarServiceInterface extends BaseServiceInterface
{
    public function create(RequestCarDTO $dto): RequestCar;

    /**
     * @param User|Authenticatable $user
     */
    public function findAllCarsPaginated(HttpRequest $request, User $user, array $columns = ['*']): LengthAwarePaginator;

    /**
     * @param User|Authenticatable $user
     */
    public function deleteRequestCar(int $requestId, User $user): RequestCar;

    /**
     * @param User|Authenticatable $user
     */
    public function findByRequestId(int $requestId, User $user): RequestCar;

    public function getStatusByStatusCurrent(string $code, string $roleName): Collection;

    public function transferRequest(int $requestCarId, RequestCarDTO $dto): RequestCar;

    public function cancelRequest(CancelRequestDTO $dto): object;

    public function approvedRequest(RequestCarDTO $dto): Request;

    public function getBusyDaysForProposalCalendar(): array;

    public function proposalRequest(RequestCarDTO $dto): Request;

    public function responseRejectRequest(int $requestId, RequestDTO $dto): Request;

    public function uploadZipImages(int $id, RequestCarDTO $dto): void;

    public function addExtraCarInformation(int $id, RequestCarDTO $dto): void;
}