<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\ActionRequestNotification;

interface ActionRequestNotificationServiceInterface extends BaseServiceInterface
{
    public function create(int $requestNotificationId, int $typeId): ActionRequestNotification;

    /**
     * @return void
     */
    public function wasAnswered(int $notificationId);
}