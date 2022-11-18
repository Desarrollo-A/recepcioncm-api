<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ActionRequestNotificationServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Core\BaseApiController;
use App\Http\Resources\Notification\NotificationResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class NotificationController extends BaseApiController
{
    private $notificationService;
    private $actionRequestNotificationService;

    public function __construct(NotificationServiceInterface              $notificationService,
                                ActionRequestNotificationServiceInterface $actionRequestNotificationService)
    {
        $this->middleware('role.permission:' . NameRole::ADMIN . ','
            . NameRole::APPLICANT . ',' . NameRole::RECEPCIONIST)
            ->only('getAllNotificationUnread', 'readNotification', 'readAllNotification',
                'existUnreadNotifications', 'show');
        $this->middleware('role.permission:' . NameRole::APPLICANT)
            ->only('wasAnswered');
        $this->middleware('role.permission:'.NameRole::ADMIN)
            ->only('confirmRequest');

        $this->notificationService = $notificationService;
        $this->actionRequestNotificationService = $actionRequestNotificationService;
    }

    public function show(int $id): JsonResponse
    {
        $notification = $this->notificationService->findById($id);
        return $this->showOne(new NotificationResource($notification));
    }

    public function getAllNotificationLast5Days(): JsonResponse
    {
        $userId = auth()->id();
        $notifications = $this->notificationService->getAllNotificationLast5Days($userId);
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

    public function wasAnswered(int $notificationId): JsonResponse
    {
        $this->actionRequestNotificationService->wasAnswered($notificationId);
        return $this->noContentResponse();
    }

    public function confirmRequest(): JsonResponse
    {
        $this->notificationService->createConfirmNotification();
        return $this->noContentResponse();
    }
}
