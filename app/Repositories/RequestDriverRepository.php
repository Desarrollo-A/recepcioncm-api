<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestDriverRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\RequestDriver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class RequestDriverRepository extends BaseRepository implements RequestDriverRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|RequestDriver
     */
    protected $entity;

    public function __construct(RequestDriver $requestDriver)
    {
        $this->entity = $requestDriver;
    }

    public function findByRequestId(int $requestId): RequestDriver
    {
        return $this->entity
            ->with(['pickupAddress', 'pickupAddress.country', 'arrivalAddress', 'arrivalAddress.country', 'request',
                'request.user', 'request.status', 'request.cancelRequest', 'request.cancelRequest.user', 
                'driverRequestSchedule', 'driverRequestSchedule.carSchedule', 'driverRequestSchedule.driverSchedule',
                'driverRequestSchedule.carSchedule.car', 'driverRequestSchedule.driverSchedule.driver', 
                'pickupAddress.office', 'arrivalAddress.office'])
            ->where('request_id', $requestId)
            ->firstOrFail();
    }
}