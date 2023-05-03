<?php

namespace App\Observers;

use App\Contracts\Services\MovementRequestServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Models\Package;

class PackageObserver
{
    private $notificationService;
    private $movementRequestService;

    function __construct(
        NotificationServiceInterface $notificationService,
        MovementRequestServiceInterface $movementRequestService
    )
    {
        $this->notificationService = $notificationService;
        $this->movementRequestService = $movementRequestService;
    }

    public function created(Package $package)
    {
        $data = $package->fresh(['request', 'request.user']);
        $this->notificationService->createRequestPackageNotification($data);
        $this->movementRequestService->create($data->request_id, auth()->id(), 'Creación de solicitud');
    }
}   