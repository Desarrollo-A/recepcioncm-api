<?php

namespace App\Services;

use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Services\LookupServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

class LookupService extends BaseService implements LookupServiceInterface
{
    protected $entityRepository;

    public function __construct(LookupRepositoryInterface $lookupRepository)
    {
        $this->entityRepository = $lookupRepository;
    }

    /**
     * @throws CustomErrorException
     * @return void
     */
    public function validateLookup(int $lookupId, int $type, string $message = 'Lookup no válido')
    {
        $lookups = $this->entityRepository->findAllByType($type, ['id']);
        $exist = $lookups->contains(function ($lookup) use ($lookupId) {
            return $lookup->id === $lookupId;
        });
        if (!$exist) {
            throw new CustomErrorException($message, Response::HTTP_BAD_REQUEST);
        }
    }

    public function findAllByType(int $type): Collection
    {
        return $this->entityRepository->findAllByType($type);
    }
}