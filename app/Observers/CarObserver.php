<?php

namespace App\Observers;

use App\Contracts\Services\CarServiceInterface;
use App\Models\Car;

class CarObserver
{
    private $carService;

    public function __construct(CarServiceInterface $carService)
    {
        $this->carService = $carService;
    }

    public function updated(Car $car): void
    {
        $this->carService->clearRelationWithDriver($car->id, $car->status_id);
    }
}
