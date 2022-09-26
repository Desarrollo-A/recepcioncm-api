<?php

namespace App\Services;

use App\Contracts\Repositories\StateRepositoryInterface;
use App\Contracts\Services\StateServiceInterface;
use App\Core\BaseService;
use Illuminate\Database\Eloquent\Collection;

class StateService extends BaseService implements StateServiceInterface
{
    protected $entityRepository;

    public function __construct(StateRepositoryInterface $stateRepository)
    {
        $this->entityRepository = $stateRepository;
    }

    public function getAll(): Collection
    {
        return $this->entityRepository->getAll();
    }
}