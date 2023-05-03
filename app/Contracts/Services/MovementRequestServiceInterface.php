<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\MovementRequest;

interface MovementRequestServiceInterface extends BaseServiceInterface
{
    public function create(int $requestId, int $userId, string $description): MovementRequest;

    public function bulkInsert(array $data): bool;
}