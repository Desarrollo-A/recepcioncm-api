<?php

namespace App\Repositories;

use App\Contracts\Repositories\ConfirmNotificationRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\ConfirmNotification;

class ConfirmNotificationRepository extends BaseRepository implements ConfirmNotificationRepositoryInterface
{
    protected $entity;

    public function __construct(ConfirmNotification $confirmNotification)
    {
        $this->entity = $confirmNotification;
    }
}