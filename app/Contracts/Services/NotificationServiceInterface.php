<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Notification;
use App\Models\Request;
use App\Models\RequestRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface NotificationServiceInterface extends BaseServiceInterface
{
    public function createRequestRoomNotification(RequestRoom $requestRoom): Notification;

    public function getAllNotificationLast5Days(int $userId): Collection;

    public function newOrResponseToApprovedRequestRoomNotification(Request $request): Notification;

    public function newToProposalRequestRoomNotification(Request $request): Notification;

    /**
     * @return void
     */
    public function newToDeletedRequestRoomNotification(Request $request);

    public function approvedToCancelledRequestRoomNotification(Request $request, User $user): Notification;

    public function proposalToRejectedOrResponseRequestRoomNotification(Request $request): Notification;

    /**
     * @return void
     */
    public function readNotification(int $id);

    /**
     * @return void
     */
    public function readAllNotificationUser(int $userId);

    /**
     * @return void
     */
    public function createConfirmNotification();
}