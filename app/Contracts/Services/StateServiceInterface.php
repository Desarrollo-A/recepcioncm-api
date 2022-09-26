<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;

interface StateServiceInterface extends BaseServiceInterface
{
    public function getAll(): Collection;
}