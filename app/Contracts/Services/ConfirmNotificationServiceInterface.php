<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\ConfirmNotification;

interface ConfirmNotificationServiceInterface extends BaseServiceInterface
{
    public function create(int $requestNotificationId): ConfirmNotification;

    /**
     * @return void
     */
    public function wasAnswered(int $notificationId);
}