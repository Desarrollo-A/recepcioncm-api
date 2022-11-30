<?php
namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;

interface OfficeServiceInterface extends BaseServiceInterface
{
    public function getOfficeByStateWithDriver(int $stateId): Collection;

    public function getByStateWithDriverWithoutOffice(int $officeId): Collection;
}