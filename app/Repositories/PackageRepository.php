<?php

namespace App\Repositories;

use App\Contracts\Repositories\PackageRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class PackageRepository extends BaseRepository implements PackageRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|Package
     */
    protected $entity;

    public function __construct(Package $package)
    {
        $this->entity = $package;
    }

    public function findByRequestId(int $requestId): Package
    {
        return $this->entity
            ->where('request_id', $requestId)
            ->firstOrFail();
    }

    public function findById(int $id, array $columns = ['*'])
    {
        return $this->entity
            ->with(['pickupAddress', 'pickupAddress.country', 'arrivalAddress', 'arrivalAddress.country', 'request',
                'request.user', 'request.status', 'request.cancelRequest', 'request.cancelRequest.user'])
            ->findOrFail($id, $columns);
    }

    public function getPackagesByDriverId(int $driverId, Carbon $date): Collection
    {
        return $this->entity
            ->with(['pickupAddress', 'pickupAddress.country', 'arrivalAddress', 'arrivalAddress.country', 'request'])
            ->join('driver_package_schedule','driver_package_schedule.package_id','=','packages.id')
            ->join('driver_schedule','driver_schedule.driver_schedule_id','=','driver_schedule.id')
            ->whereDate('driver_schedule.start_date', $date)
            ->where('driver_schedule.driver_id', $driverId)
            ->get(['packages.*']);
    }
}
