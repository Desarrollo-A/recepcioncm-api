<?php

namespace App\Services;

use App\Contracts\Repositories\ScoreRepositoryInterface;
use App\Contracts\Services\ScoreServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Models\Dto\ScoreDTO;
use App\Models\Score;

class ScoreService extends BaseService implements ScoreServiceInterface
{
    protected $entityRepository;

    public function __construct(ScoreRepositoryInterface $scoreRepository)
    {
        $this->entityRepository = $scoreRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function create(ScoreDTO $dto): Score
    {
        return $this->entityRepository->create($dto->toArray(['request_id', 'score', 'comment']));
    }
}