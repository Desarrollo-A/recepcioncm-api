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
     * Handle the InventoryRequest "created" event.
     *
     * @param \App\Models\InventoryRequest $inventoryRequest
     * @return void
     */
    public function created(InventoryRequest $inventoryRequest)
    {
        if ($inventoryRequest->applied) {
            $this->inventoryService->updateStockAfterApprove($inventoryRequest);
        }
    }
}