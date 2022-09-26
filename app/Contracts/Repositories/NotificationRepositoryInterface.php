<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface NotificationRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllNotificationUnread(int $userId): Collection;

    /**
     * @return void
     */
    public function massiveNotificationUserUpdate(int $userId, array $data);
}