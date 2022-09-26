<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\RequestRoom;

/**
 * @method RequestRoom create(array $data)
 * @method RequestRoom findById(int $id, array $columns = ['*'])
 */
interface RequestRoomRepositoryInterface extends BaseRepositoryInterface
{
    //
}