<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\FileDTO;
use App\Models\Dto\PerDiemDTO;
use App\Models\PerDiem;

interface PerDiemServiceInterface extends BaseServiceInterface
{
    public function store(PerDiemDTO $dto): PerDiem;

    public function update(int $id, PerDiemDTO $dto): PerDiem;

    /**
     * @param FileDTO[] $filesDTO
     */
    public function uploadBillFiles(int $id, array $filesDTO): void;
}