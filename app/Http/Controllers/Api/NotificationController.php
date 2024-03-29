<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ActionRequestNotificationServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Core\BaseApiController;
use App\Http\Resources\Notification\NotificationResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class NotificationController extends BaseApiController
{
    private $notificationService;
    private $actionRequestNotificationService;

    public function __construct(
        NotificationServiceInterface $notificationService,
        ActionRequestNotificationServiceInterface $actionRequestNotificationService
    )
    {
        $this->middleware('role.permission:'.NameRole::allRolesMiddleware())
            ->only('getAllNotificationUnread', 'readNotification', 'readAllNotification',
                'existUnreadNotifications', 'show');
        $this->middleware('role.permission:' . NameRole::APPLICANT)
            ->only('wasAnswered');

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

    public function readNotification(int $id): Response
    {
        $this->notificationService->readNotification($id);
        return $this->noContentResponse();
    }

    public function readAllNotification(): Response
    {
        $this->notificationService->readAllNotificationUser(auth()->id());
        return $this->noContentResponse();
    }

    public function wasAnswered(int $notificationId): Response
    {
        $this->actionRequestNotificationService->wasAnswered($notificationId);
        return $this->noContentResponse();
    }
}
