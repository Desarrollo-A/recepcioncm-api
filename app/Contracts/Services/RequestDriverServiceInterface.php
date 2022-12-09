<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\RequestDriverDTO;
use App\Models\RequestDriver;

interface RequestDriverServiceInterface extends BaseServiceInterface
{
    public function create(RequestDriverDTO $dto): RequestDriver;

    public function uploadAuthorizationFile(int $id, RequestDriverDTO $dto): void;
}