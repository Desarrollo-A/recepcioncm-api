<?php
namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\OfficeDTO;
use App\Models\Office;
use Illuminate\Database\Eloquent\Collection;

interface OfficeServiceInterface extends BaseServiceInterface
{
    public function getOfficeByStateId(int $stateId): Collection;

    public function getByStateWithDriverWithoutOffice(int $officeId): Collection;

    public function getOfficeByStateWithDriverAndCar(int $stateId, int $noPeople): Collection;

    public function getOfficeByStateWithCar(int $stateId, int $noPeople): Collection;

    public function getOfficeByStateWithDriverAndCarWithoutOffice(int $officeId, int $noPeople): Collection;

    public function getOfficeByStateWithCarWithoutOffice(int $officeId, int $noPeople): Collection;

    public function store(OfficeDTO $dto): Office;

    public function update(int $id, OfficeDTO $dto): Office;

    public function findAllActive(): Collection;
}