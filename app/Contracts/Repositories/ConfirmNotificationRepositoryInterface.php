<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;

interface ConfirmNotificationRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @return void
     */
    public function updatePastRecords();
}