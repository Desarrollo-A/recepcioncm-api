<?php

namespace App\Services;

use App\Contracts\Repositories\PerDiemRepositoryInterface;
use App\Contracts\Services\PerDiemServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Path;
use App\Helpers\File;
use App\Models\Dto\PerDiemDTO;
use App\Models\PerDiem;

class PerDiemService extends BaseService implements PerDiemServiceInterface
{
    protected $entityRepository;

    public function __construct(PerDiemRepositoryInterface $perDiemRepository)
    {
        $this->entityRepository = $perDiemRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function store(PerDiemDTO $dto): PerDiem
    {
        return $this->entityRepository->create($dto->toArray(['request_id', 'gasoline', 'tollbooths', 'food']));
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $requestId, PerDiemDTO $dto): PerDiem
    {
        return $this->entityRepository->update($requestId, $dto->toArray(['spent']));
    }

    /**
     * @throws CustomErrorException
     */
    public function uploadBillZip(int $requestId, PerDiemDTO $dto): void
    {
        $dto->bill_filename = File::uploadFile($dto->bill_file, Path::REQUEST_CAR_BILL_ZIP);
        $this->entityRepository->update($requestId, $dto->toArray(['bill_filename']));
    }
}