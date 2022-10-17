<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestNotificationRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\RequestNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class RequestNotificationRepository extends BaseRepository implements RequestNotificationRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|RequestNotification
     */
    protected $entity;

    public function __construct(RequestNotification $requestNotification)
    {
        $this->entity = $requestNotification;
    }
}