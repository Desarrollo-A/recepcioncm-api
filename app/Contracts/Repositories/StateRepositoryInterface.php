<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface StateRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll(): Collection;
}