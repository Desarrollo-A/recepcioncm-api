<?php

namespace App\Observers;

use App\Contracts\Services\InventoryServiceInterface;
use App\Models\InventoryRequest;

class InventoryRequestObserver
{
    private $inventoryService;

    public function __construct(InventoryServiceInterface $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * @return void
     */
    public function updated(InventoryRequest $inventoryRequest)
    {
        if ($inventoryRequest->isDirty('applied') && $inventoryRequest->applied && $inventoryRequest->quantity !== null) {
            $this->inventoryService->updateStockAfterApprove($inventoryRequest);
        }
    }
}