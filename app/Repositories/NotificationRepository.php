<?php

namespace App\Repositories;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Core\BaseRepository;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|Notification
     */
    protected $entity;

    public function __construct(Notification $notification)
    {
        $this->entity = $notification;
    }

    public function findById(int $id, array $columns = ['*']): Notification
    {
        return $this->entity
            ->with(['type', 'requestNotification', 'requestNotification.request', 'requestNotification.request.requestRoom',
                'requestNotification.request.requestRoom.room', 'requestNotification.request.requestRoom.room.office'])
            ->findOrFail($id, $columns);
    }

    public function getAllNotificationLast5Days(int $userId): Collection
    {
        return $this->entity
            ->with(['type', 'color', 'icon', 'requestNotification', 'requestNotification.request', 'requestNotification.confirmNotification'])
            ->where('user_id', $userId)
            ->whereDate('created_at', '>', now()->subDays(5))
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * @return void
     */
    public function massiveNotificationUserUpdate(int $userId, array $data)
    {
        $this->entity
            ->where('user_id', $userId)
            ->update($data);
    }
}