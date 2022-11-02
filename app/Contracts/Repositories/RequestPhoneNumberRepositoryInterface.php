<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;

interface RequestPhoneNumberRepositoryInterface extends BaseRepositoryInterface
{
    public function bulkInsert(array $data): bool;
}