<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\InventoryRequestServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Core\BaseApiController;
use Illuminate\Http\Response;

class CronJobController extends BaseApiController
{
    private $inventoryRequestService;
    private $notificationService;
    private $requestService;
    private $userService;

    public function __construct
    (
        InventoryRequestServiceInterface $inventoryRequestService,
        NotificationServiceInterface $notificationService,
        RequestServiceInterface $requestService,
        UserServiceInterface $userService
    )
    {
        $this->inventoryRequestService = $inventoryRequestService;
        $this->notificationService = $notificationService;
        $this->requestService = $requestService;
        $this->userService = $userService;
    }

    public function updateSnackCountable(): Response
    {
        $this->inventoryRequestService->addHistoryRequestSnackCountable();
        return $this->noContentResponse();
    }

    public function updateSnackUncountable(): Response
    {
        $this->inventoryRequestService->updateSnackUncountableApplied();
        return $this->noContentResponse();
    }

    public function confirmRequest(): Response
    {
        $this->notificationService->createConfirmNotification();
        return $this->noContentResponse();
    }

    public function finishedRequest(): Response
    {
        $requests = $this->requestService->changeToFinished();
        if ($requests->count() > 0) {
            $this->notificationService->createScoreRequestNotification($requests);
        }

        return $this->noContentResponse();
    }

    public function expiredRequest(): Response
    {
        $this->requestService->changeToExpired();
        return $this->noContentResponse();
    }

    public function removeOldTokens(): Response
    {
        $this->userService->removeOldTokens();
        return $this->noContentResponse();
    }
}
