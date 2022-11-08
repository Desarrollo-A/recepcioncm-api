<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\ScoreDTO;
use App\Models\Score;

interface ScoreServiceInterface extends BaseServiceInterface
{
    public function create(ScoreDTO $dto): Score;
}