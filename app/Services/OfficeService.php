<?php
namespace App\Services;

use App\Contracts\Repositories\OfficeRepositoryInterface;
use App\Contracts\Services\OfficeServiceInterface;
use App\Core\BaseService;
use Illuminate\Database\Eloquent\Collection;

class OfficeService extends BaseService implements OfficeServiceInterface
{
    protected $entityRepository;

    public function __construct(OfficeRepositoryInterface $officeRepository)
    {
        $this->entityRepository = $officeRepository;
    }

    public function getOfficeByStateWithDriver(int $stateId): Collection
    {
        return $this->entityRepository->getOfficeByStateWithDriver($stateId);
    }

    public function getByStateWithDriverWithoutOffice(int $officeId): Collection
    {
        $office = $this->entityRepository->findById($officeId);
        return $this->entityRepository->getByStateWithDriverWithoutOffice($office);
    }

    public function getOfficeByStateWithDriverAndCar(int $stateId, int $noPeople): Collection
    {
        return $this->entityRepository->getOfficeByStateWithDriverAndCar($stateId, $noPeople);
    }

    public function getOfficeByStateWithCar(int $stateId, int $noPeople): Collection
    {
        return $this->entityRepository->getOfficeByStateWithCar($stateId, $noPeople);
    }
}