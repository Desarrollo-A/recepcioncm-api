<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Inventory;
use App\Models\Notification;
use App\Models\Package;
use App\Models\Request;
use App\Models\RequestDriver;
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

    /**
     * @return void
     */
    public function minimumStockNotification(Inventory $inventory);

    public function createScoreRequestNotification(Collection $requests);

    public function createRequestPackageNotification(Package $package): Notification;
    
    public function deleteRequestPackageNotification (Package $package): void;

    public function cancelRequestPackageNotification(Request $request, User $informationUserAndRole): Notification;

    public function transferPackageRequestNotification(Package $packageTransfer): Notification;

    public function approvedPackageRequestNotification(Package $packageApproved): Notification;

    public function onRoadPackageRequestNotification(Request $requestPackageOnRoad): Notification;

    public function deliveredPackageRequestNotification(Request $requestPackageDelivered): Notification;

    public function createRequestDriverNotification(RequestDriver $requestDriver): Notification;

    public function deleteRequestDriverNotification(RequestDriver $requestDriver): void;

    public function cancelRequestDriverNotification(Request $request, User $user): Notification;

    public function transferRequestDriverNotification(RequestDriver $requestDriver): Notification;

    public function approvedRequestDriverNotification(Request $request): Notification;
}