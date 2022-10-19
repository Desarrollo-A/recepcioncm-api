<?php

namespace App\Observers;

use App\Contracts\Services\InventoryHistoryServiceInterface;
use App\Contracts\Services\InventoryServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Models\Inventory;

class InventoryObserver
{
    private $inventoryHistoryService;
    private $inventoryService;
    private $notificationService;

    public function __construct(InventoryHistoryServiceInterface $inventoryHistoryService,
                                InventoryServiceInterface $inventoryService,
                                NotificationServiceInterface $notificationService)
    {
        $this->inventoryHistoryService = $inventoryHistoryService;
        $this->inventoryService = $inventoryService;
        $this->notificationService = $notificationService;
    }

    /**
     * @return void
     */
    public function created(Inventory $inventory)
    {
        $this->inventoryService->updateCode($inventory);
    }

    /**
     * @return void
     */
    public function updated(Inventory $inventory)
    {
        if ($inventory->isDirty('stock')) {
            if ($inventory->stock < $inventory->getOriginal('stock')) {
                $this->inventoryHistoryService->store($inventory);
                if ($inventory->stock <= $inventory->minimum_stock) {
                    $this->notificationService->minimumStockNotification($inventory);
                }
            }
        }
    }
}