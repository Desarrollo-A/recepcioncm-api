<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\InventoryRequestDTO;
use App\Models\InventoryRequest;
use Illuminate\Database\Eloquent\Collection;

interface InventoryRequestServiceInterface extends BaseServiceInterface
{
    public function create(InventoryRequestDTO $dto): InventoryRequest;

    public function createSnack(InventoryRequestDTO $dto, int $officeId): InventoryRequest;

    /**
     * @return void
     */
    public function updateSnack(int $requestId, int $inventoryId, InventoryRequestDTO $dto, int $officeId);

    public function deleteSnack(int $requestId, int $inventoryId): InventoryRequest;

    public function deleteSnacks(int $requestId): Collection;

    /**
     * @return void
     */
    public function updateSnackUncountableApplied();
}