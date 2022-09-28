<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\NotificationServiceInterface;
use App\Core\BaseApiController;
use App\Http\Resources\Notification\NotificationResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class NotificationController extends BaseApiController
{
    private $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->middleware('role.permission:' . NameRole::ADMIN . ','
            . NameRole::APPLICANT . ',' . NameRole::RECEPCIONIST)
            ->only('getAllNotificationUnread', 'readNotification', 'readAllNotification', 'existUnreadNotifications');
        $this->notificationService = $notificationService;
    }

    public function getAllNotificationUnread(): JsonResponse
    {
        $userId = auth()->id();
        $notifications = $this->notificationService->getAllNotificationUnread($userId);
        return $this->showAll(NotificationResource::collection($notifications));
    }

    public function readNotification(int $id): JsonResponse
    {
        $this->notificationService->readNotification($id);
        return $this->noContentResponse();
    }

    public function readAllNotification(): JsonResponse
    {
        $this->notificationService->readAllNotificationUser(auth()->id());
        return $this->noContentResponse();
    }
}
