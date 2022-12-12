<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\RequestCarDTO;
use App\Models\RequestCar;

interface RequestCarServiceInterface extends BaseServiceInterface
{
    public function create(RequestCarDTO $dto): RequestCar;

    public function uploadAuthorizationFile(int $id, RequestCarDTO $dto): void;
}