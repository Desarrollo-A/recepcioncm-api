<?php

namespace App\Services;

use App\Contracts\Repositories\RequestPhoneNumberRepositoryInterface;
use App\Contracts\Services\RequestPhoneNumberServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Models\Dto\RequestPhoneNumberDTO;
use App\Models\RequestPhoneNumber;

class RequestPhoneNumberService extends BaseService implements RequestPhoneNumberServiceInterface
{
    protected $entityRepository;

    public function __construct(RequestPhoneNumberRepositoryInterface $requestPhoneNumberRepository)
    {
        $this->entityRepository = $requestPhoneNumberRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function create(RequestPhoneNumberDTO $dto): RequestPhoneNumber
    {
        return $this->entityRepository->create($dto->toArray(['name', 'phone', 'request_id']));
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $id, RequestPhoneNumberDTO $dto): RequestPhoneNumber
    {
        return $this->entityRepository->update($id, $dto->toArray(['name', 'phone', 'request_id']));
    }
}