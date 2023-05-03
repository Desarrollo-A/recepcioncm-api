<?php

namespace App\Services;

use App\Contracts\Repositories\MovementRequestRepositoryInterface;
use App\Contracts\Services\MovementRequestServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Models\Dto\MovementRequestDTO;
use App\Models\MovementRequest;

class MovementRequestService extends BaseService implements MovementRequestServiceInterface
{
    protected $entityRepository;

    public function __construct(MovementRequestRepositoryInterface $movementRequestRepository)
    {
        $this->entityRepository = $movementRequestRepository;
    }


    /**
     * @throws CustomErrorException
     */
    public function create(int $requestId, int $userId, string $description): MovementRequest
    {
        $dto = new MovementRequestDTO([
            'request_id' => $requestId,
            'user_id' => $userId,
            'description' => $description
        ]);

        return $this->entityRepository->create($dto->toArray());
    }

    public function bulkInsert(array $data): bool
    {
        return $this->entityRepository->bulkInsert($data);
    }
}