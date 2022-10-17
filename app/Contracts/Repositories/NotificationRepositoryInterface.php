<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Notification create(array $data)
 * @method Notification findById(int $id, array $columns = ['*'])
 */
interface NotificationRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllNotificationLast5Days(int $userId): Collection;

    /**
     * @return void
     */
    public function massiveNotificationUserUpdate(int $userId, array $data);
}