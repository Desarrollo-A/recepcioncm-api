<?php
namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;

interface OfficeServiceInterface extends BaseServiceInterface
{
    public function getOfficeByStateWithDriver(int $stateId): Collection;

    public function getByStateWithDriverWithoutOffice(int $officeId): Collection;

    public function getOfficeByStateWithDriverAndCar(int $stateId, int $noPeople): Collection;

    public function getOfficeByStateWithCar(int $stateId, int $noPeople): Collection;

    public function getOfficeByStateWithDriverAndCarWithoutOffice(int $officeId, int $noPeople): Collection;

    public function getOfficeByStateWithCarWithoutOffice(int $officeId, int $noPeople): Collection;
}