<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\RequestDTO;
use App\Models\Inventory;
use App\Models\Package;
use App\Models\Request;
use App\Models\RequestCar;
use App\Models\RequestDriver;
use App\Models\RequestRoom;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

interface NotificationServiceInterface extends BaseServiceInterface
{
    public function createRequestRoomNotification(RequestRoom $requestRoom): void;

    public function getAllNotificationLast5Days(int $userId): Collection;

    public function newOrResponseToApprovedRequestRoomNotification(Request $request): void;

    public function newToProposalRequestRoomNotification(Request $request): void;

    public function newToDeletedRequestRoomNotification(Request $request): void;

    /**
     * @param User|Authenticatable $user
     */
    public function approvedToCancelledRequestRoomNotification(Request $request, User $user): void;

    public function proposalToRejectedOrResponseRequestRoomNotification(Request $request): void;

    public function readNotification(int $id): void;

    public function readAllNotificationUser(int $userId): void;

    public function createConfirmNotification(): void;

    public function minimumStockNotification(Inventory $inventory): void;

    public function createScoreRequestNotification(Collection $requests): void;

    public function createRequestPackageNotification(Package $package): void;
    
    public function deleteRequestPackageNotification (Package $package): void;

    /**
     * @param User|Authenticatable $user
     */
    public function cancelRequestPackageNotification(Request $request, User $user): void;

    public function transferPackageRequestNotification(Package $package): void;

    public function approvedPackageRequestNotification(Package $package, int $driverId = null): void;

    public function onRoadPackageRequestNotification(Request $request): void;

    public function deliveredPackageRequestNotification(Request $request): void;

    public function proposalPackageRequestNotification(Request $requestPackageProposal): void;

    public function responseRejectRequestNotification(Request $request): void;

    public function createRequestDriverNotification(RequestDriver $requestDriver): void;

    public function deleteRequestDriverNotification(RequestDriver $requestDriver): void;
    
    /**
     * @param User|Authenticatable $user
     */
    public function cancelRequestDriverNotification(Request $request, User $user): void;

    public function transferRequestDriverNotification(RequestDriver $requestDriver): void;

    public function approvedRequestDriverNotification(Request $request, int $driverId = null): void;

    public function proposalDriverRequestNotification(Request $proposalDriverRequest): void;

    public function responseRejectRequestDriverNotification(Request $requestDriverResponseReject):void;

    public function createRequestCarNotification(RequestCar $requestCar): void;

    public function deleteRequestCarNotification(RequestCar $requestCar): void;

    public function transferRequestCarNotification(RequestCar $requestCar): void;

    /**
     * @param User|Authenticatable $user
     */
    public function cancelRequestCarNotification(Request $request, User $user): void;

    public function approvedRequestCarNotification(Request $request): void;
}