<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\RequestPhoneNumberDTO;
use App\Models\RequestPhoneNumber;

interface RequestPhoneNumberServiceInterface extends BaseServiceInterface
{
    public function create(RequestPhoneNumberDTO $dto): RequestPhoneNumber;

    public function update(int $id, RequestPhoneNumberDTO $dto): RequestPhoneNumber;
}