<?php

namespace App\Repositories;

use App\Contracts\Repositories\HeavyShipmentRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\HeavyShipment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class HeavyShipmentRepository extends BaseRepository implements HeavyShipmentRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|HeavyShipment
     */
    protected $entity;

    public function __construct(HeavyShipment $heavyShipment)
    {
        $this->entity = $heavyShipment;
    }
}