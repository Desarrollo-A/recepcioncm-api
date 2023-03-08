<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\InventoryRequestServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Core\BaseApiController;
use Illuminate\Http\JsonResponse;

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

    public function updateSnackCountable(): JsonResponse
    {
        $this->inventoryRequestService->addHistoryRequestSnackCountable();
        return $this->noContentResponse();
    }

    public function updateSnackUncountable(): JsonResponse
    {
        $this->inventoryRequestService->updateSnackUncountableApplied();
        return $this->noContentResponse();
    }

    public function confirmRequest(): JsonResponse
    {
        $this->notificationService->createConfirmNotification();
        return $this->noContentResponse();
    }

    public function finishedRequest(): JsonResponse
    {
        $requests = $this->requestService->changeToFinished();
        if ($requests->count() > 0) {
            $this->notificationService->createScoreRequestNotification($requests);
        }

        return $this->noContentResponse();
    }

    public function expiredRequest(): JsonResponse
    {
        $this->requestService->changeToExpired();
        return $this->noContentResponse();
    }

    public function removeOldTokens(): JsonResponse
    {
        $this->userService->removeOldTokens();
        return $this->noContentResponse();
    }
}
