<?php

namespace App\Console\Commands\InventoryRequest;

use App\Contracts\Services\InventoryRequestServiceInterface;
use Illuminate\Console\Command;

class UpdateSnackCountable extends Command
{
    private $inventoryRequestService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory-request:countable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agregar al historial de movimientos los snacks contables';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(InventoryRequestServiceInterface $inventoryRequestService)
    {
        parent::__construct();
        $this->inventoryRequestService = $inventoryRequestService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->inventoryRequestService->addHistoryRequestSnackCountable();
    }
}
