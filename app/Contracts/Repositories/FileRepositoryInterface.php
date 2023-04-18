<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\File;
use App\Models\PerDiem;
use App\Models\RequestCar;

interface FileRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param PerDiem|RequestCar $model
     * @param File[] $files
     */
    public function saveManyFiles($model, array $files): void;
}