<?php

namespace App\Services;

use App\Contracts\Repositories\RequestEmailRepositoryInterface;
use App\Contracts\Services\RequestEmailServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Models\Dto\RequestEmailDTO;
use App\Models\RequestEmail;

class RequestEmailService extends BaseService implements RequestEmailServiceInterface
{
    protected $entityRepository;

    public function __construct(RequestEmailRepositoryInterface $requestEmailRepository)
    {
        $this->entityRepository = $requestEmailRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function create(RequestEmailDTO $dto): RequestEmail
    {
        return $this->entityRepository->create($dto->toArray(['name', 'email', 'request_id']));
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $id, RequestEmailDTO $dto): RequestEmail
    {
        return $this->entityRepository->update($id, $dto->toArray(['name', 'email', 'request_id']));
    }
}