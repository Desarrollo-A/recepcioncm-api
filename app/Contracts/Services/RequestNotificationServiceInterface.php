<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\RequestNotification;

interface RequestNotificationServiceInterface extends BaseServiceInterface
{
    public function create(int $requestId, int $notificationId): RequestNotification;
}