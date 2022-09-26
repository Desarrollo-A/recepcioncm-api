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

    public function getAllNotificationUnread(int $userId): Collection
    {
        return $this->entity
            ->with('type')
            ->where('user_id',$userId)
            ->where('is_read',false)
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