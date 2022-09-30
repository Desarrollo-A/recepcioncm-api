<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\Office;

interface OfficeRepositoryInterface extends BaseRepositoryInterface
{
    public function findByName(string $name): Office;
}