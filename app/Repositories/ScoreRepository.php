<?php

namespace App\Repositories;

use App\Contracts\Repositories\ScoreRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Score;

class ScoreRepository extends BaseRepository implements ScoreRepositoryInterface
{
    protected $entity;

    public function __construct(Score $score)
    {
        $this->entity = $score;
    }
}