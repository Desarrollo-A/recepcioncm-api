<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\RequestRoom;
use App\Models\User;

/**
 * @method RequestRoom create(array $data)
 * @method RequestRoom findById(int $id, array $columns = ['*'])
 */
interface RequestRoomRepositoryInterface extends BaseRepositoryInterface
{
    public function getDataCalendar(User $user);

    public function getSummaryOfDay(User $user);
}