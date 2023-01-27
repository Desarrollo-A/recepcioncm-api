<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\InventoryRequestServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestServiceInterface;
use App\Core\BaseApiController;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class CronJobController extends BaseApiController
{
    private $inventoryRequestService;
    private $notificationService;
    private $requestService;

    public function __construct(InventoryRequestServiceInterface $inventoryRequestService,
                                NotificationServiceInterface $notificationService,
                                RequestServiceInterface $requestService)
    {
        $this->middleware('role.permission:'.NameRole::ADMIN);

        $this->inventoryRequestService = $inventoryRequestService;
        $this->notificationService = $notificationService;
        $this->requestService = $requestService;
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
        // if ($requests->count() > 0) {
        //     $this->notificationService->createScoreRequestNotification($requests);
        // }

        return $this->noContentResponse();
    }

    public function expiredRequest(): JsonResponse
    {
        $this->requestService->changeToExpired();
        return $this->noContentResponse();
    }
}
