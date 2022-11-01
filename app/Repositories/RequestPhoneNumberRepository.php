<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestPhoneNumberRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\RequestPhoneNumber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class RequestPhoneNumberRepository extends BaseRepository implements RequestPhoneNumberRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|RequestPhoneNumber
     */
    protected $entity;

    public function __construct(RequestPhoneNumber $requestPhoneNumber)
    {
        $this->entity = $requestPhoneNumber;
    }

    public function bulkInsert(array $data): bool
    {
        return $this->entity->insert($data);
    }
}