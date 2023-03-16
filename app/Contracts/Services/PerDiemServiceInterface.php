<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\PerDiemDTO;
use App\Models\PerDiem;

interface PerDiemServiceInterface extends BaseServiceInterface
{
    public function store(PerDiemDTO $dto): PerDiem;

    public function update(int $requestId, PerDiemDTO $dto): PerDiem;

    public function uploadBillZip(int $requestId, PerDiemDTO $dto): void;
}