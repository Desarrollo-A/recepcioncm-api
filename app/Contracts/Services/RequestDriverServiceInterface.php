<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\RequestDriverDTO;
use App\Models\RequestDriver;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Pagination\LengthAwarePaginator;

interface RequestDriverServiceInterface extends BaseServiceInterface
{
    public function create(RequestDriverDTO $dto): RequestDriver;

    public function uploadAuthorizationFile(int $id, RequestDriverDTO $dto): void;

    public function findAllDriversPaginated(HttpRequest $request, User $user, array $columns = ['*']): LengthAwarePaginator;
}