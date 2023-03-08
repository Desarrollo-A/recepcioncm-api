<?php

namespace App\Observers;

use App\Contracts\Services\DriverServiceInterface;
use App\Models\User;

class DriverObserver
{
    private $driverService;

    public function __construct(DriverServiceInterface $driverService)
    {
        $this->driverService = $driverService;
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        $this->driverService->clearRelationWithCar($user->id, $user->status_id);
    }
}
