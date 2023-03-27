<?php
namespace App\Services;

use App\Contracts\Repositories\AddressRepositoryInterface;
use App\Contracts\Repositories\OfficeRepositoryInterface;
use App\Contracts\Services\OfficeServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Models\Dto\OfficeDTO;
use App\Models\Office;
use Illuminate\Database\Eloquent\Collection;

class OfficeService extends BaseService implements OfficeServiceInterface
{
    protected $entityRepository;
    protected $addressRepository;

    public function __construct(OfficeRepositoryInterface $officeRepository,
                                AddressRepositoryInterface $addressRepository)
    {
        $this->entityRepository = $officeRepository;
        $this->addressRepository = $addressRepository;
    }

    public function getOfficeByStateId(int $stateId): Collection
    {
        return $this->entityRepository->getOfficeByStateId($stateId);
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

    public function getOfficeByStateWithDriverAndCarWithoutOffice(int $officeId, int $noPeople): Collection
    {
        $office = $this->entityRepository->findById($officeId);
        return $this->entityRepository->getOfficeByStateWithDriverAndCarWithoutOffice($office, $noPeople);
    }

    public function getOfficeByStateWithCarWithoutOffice(int $officeId, int $noPeople): Collection
    {
        $office = $this->entityRepository->findById($officeId);
        return $this->entityRepository->getOfficeByStateWithCarWithoutOffice($office, $noPeople);
    }

    /**
     * @throws CustomErrorException
     */
    public function store(OfficeDTO $dto): Office
    {
        $address = $this->addressRepository->create($dto->address->toArray([
            'street', 'num_ext', 'num_int', 'suburb', 'postal_code', 'state', 'country_id'
        ]));
        $dto->address_id = $address->id;

        return $this->entityRepository->create($dto->toArray(['name', 'address_id', 'state_id']));
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $id, OfficeDTO $dto): Office
    {
        $this->addressRepository->update($dto->address_id, $dto->address->toArray([
            'street', 'num_ext', 'num_int', 'suburb', 'postal_code', 'state', 'country_id'
        ]));

        return $this->entityRepository->update($id, $dto->toArray(['name', 'state_id']));
    }

    public function delete(int $id): void
    {
        $office = $this->entityRepository->findById($id);
        $this->entityRepository->delete($id);
        $this->addressRepository->delete($office->address_id);
    }
}