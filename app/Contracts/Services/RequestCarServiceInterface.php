<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\RequestCarDTO;
use App\Models\RequestCar;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request as HttpRequest;

interface RequestCarServiceInterface extends BaseServiceInterface
{
    public function create(RequestCarDTO $dto): RequestCar;

    public function uploadAuthorizationFile(int $id, RequestCarDTO $dto): void;

    public function findAllCarsPaginated(HttpRequest $request, User $user, array $columns = ['*']): LengthAwarePaginator;
}