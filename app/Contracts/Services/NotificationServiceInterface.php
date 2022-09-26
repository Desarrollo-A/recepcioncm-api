<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Request;
use App\Models\RequestRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface NotificationServiceInterface extends BaseServiceInterface
{
    /**
     * @return void
     */
    public function createRequestRoomNotification(RequestRoom $requestRoom);

    public function getAllNotificationUnread(int $userId): Collection;

    /**
     * @return void
     */
    public function newOrResponseToApprovedRequestRoomNotification(Request $request);

    /**
     * @return void
     */
    public function newToProposalRequestRoomNotification(Request $request);

    /**
     * @return void
     */
    public function newToDeletedRequestRoomNotification(Request $request);

    /**
     * @return void
     */
    public function approvedToCancelledRequestRoomNotification(Request $request, User $user);

    /**
     * @return void
     */
    public function proposalToRejectedOrResponseRequestRoomNotification(Request $request);

    /**
     * @return void
     */
    public function readNotification(int $id);

    /**
     * @return void
     */
    public function readAllNotificationUser(int $userId);
}