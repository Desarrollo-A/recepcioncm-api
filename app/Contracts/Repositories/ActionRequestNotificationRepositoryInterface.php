<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;

interface ActionRequestNotificationRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @return void
     */
    public function updatePastRecords();
}