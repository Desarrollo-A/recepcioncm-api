<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\Office;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Office create(array $data)
 * @method Office update(int $id, array $data)
 */
interface OfficeRepositoryInterface extends BaseRepositoryInterface
{
    public function findByName(string $name): Office;
    
    public function getOfficeByStateId(int $stateId): Collection;

    public function getByStateWithDriverWithoutOffice(Office $office): Collection;

    public function getOfficeByStateWithDriverAndCar(int $stateId, int $noPeople): Collection;

    public function getOfficeByStateWithCar(int $stateId, int $noPeople): Collection;

    public function getOfficeByStateWithDriverAndCarWithoutOffice(Office $office, int $noPeople): Collection;

    public function getOfficeByStateWithCarWithoutOffice(Office $office, int $noPeople): Collection;

    public function findAllActive(): Collection;
}