<?php

namespace App\Services;

use App\Contracts\Repositories\FileRepositoryInterface;
use App\Contracts\Repositories\PerDiemRepositoryInterface;
use App\Contracts\Services\PerDiemServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Path;
use App\Models\Dto\PerDiemDTO;
use App\Models\File;
use App\Models\PerDiem;

class PerDiemService extends BaseService implements PerDiemServiceInterface
{
    protected $entityRepository;
    protected $fileRepository;

    public function __construct(
        PerDiemRepositoryInterface $perDiemRepository,
        FileRepositoryInterface $fileRepository
    )
    {
        $this->entityRepository = $perDiemRepository;
        $this->fileRepository = $fileRepository;
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
    public function update(int $id, PerDiemDTO $dto): PerDiem
    {
        return $this->entityRepository->update($id, $dto->toArray(['spent']));
    }

    public function uploadBillFiles(int $id, array $filesDTO): void
    {
        $perDiem = $this->entityRepository->findById($id, ['id']);
        $files = [];

        foreach ($filesDTO as $dto) {
            $filename = \App\Helpers\File::uploadFile($dto->file, Path::FILES);
            $files[] = new File(['filename' => $filename]);
        }

        $this->fileRepository->saveManyFiles($perDiem, $files);
    }
}