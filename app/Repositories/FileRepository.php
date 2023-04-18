<?php

namespace App\Repositories;

use App\Contracts\Repositories\FileRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class FileRepository extends BaseRepository implements FileRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|File
     */
    protected $entity;

    public function __construct(File $file)
    {
        $this->entity = $file;
    }

    public function saveManyFiles($model, array $files): void
    {
        $model->files()->saveMany($files);
    }
}