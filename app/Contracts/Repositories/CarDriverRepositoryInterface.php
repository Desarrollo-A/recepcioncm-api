<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;

interface CarDriverRepositoryInterface extends BaseRepositoryInterface
{

    public function deleteByCarId($carId): bool;
}