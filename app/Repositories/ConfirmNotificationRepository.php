<?php

namespace App\Repositories;

use App\Contracts\Repositories\ConfirmNotificationRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\ConfirmNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ConfirmNotificationRepository extends BaseRepository implements ConfirmNotificationRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|ConfirmNotification
     */
    protected $entity;

    public function __construct(ConfirmNotification $confirmNotification)
    {
        $this->entity = $confirmNotification;
    }

    public function updatePastRecords()
    {
        $this->entity
            ->whereDate('created_at', '<', now())
            ->update(['is_answered' => true]);
    }
}