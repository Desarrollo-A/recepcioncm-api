<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\Office;
use Illuminate\Database\Eloquent\Collection;

interface OfficeRepositoryInterface extends BaseRepositoryInterface
{
    public function findByName(string $name): Office;
    
    public function getOfficeByStateWithDriver(int $stateId): Collection;
}