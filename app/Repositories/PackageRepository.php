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
            ->with(['pickupAddress', 'pickupAddress.country', 'arrivalAddress', 'arrivalAddress.country', 'request',
                'request.user', 'request.status', 'request.cancelRequest', 'request.cancelRequest.user', 'request.proposalRequest',
                'driverPackageSchedule', 'driverPackageSchedule.carSchedule', 'driverPackageSchedule.driverSchedule',
                'driverPackageSchedule.carSchedule.car', 'driverPackageSchedule.driverSchedule.driver', 
                'pickupAddress.office', 'arrivalAddress.office'])
            ->where('request_id', $requestId)
            ->firstOrFail();
    }

    public function findById(int $id, array $columns = ['*']): Package
    {
        return $this->entity
            ->with(['pickupAddress', 'pickupAddress.country', 'arrivalAddress', 'arrivalAddress.country', 'request',
                'request.user', 'request.status', 'request.cancelRequest', 'request.cancelRequest.user', 'request.proposalRequest',
                'driverPackageSchedule', 'driverPackageSchedule.carSchedule', 'driverPackageSchedule.driverSchedule',
                'driverPackageSchedule.carSchedule.car', 'driverPackageSchedule.driverSchedule.driver'])
            ->findOrFail($id, $columns);
    }

    public function getPackagesByDriverId(int $driverId, Carbon $date): Collection
    {
        return $this->entity
            ->with(['pickupAddress', 'pickupAddress.country', 'arrivalAddress', 'arrivalAddress.country', 'request'])
            ->join('driver_package_schedules','driver_package_schedules.package_id','=','packages.id')
            ->join('driver_schedules','driver_package_schedules.driver_schedule_id','=','driver_schedules.id')
            ->whereDate('driver_schedules.start_date', $date)
            ->where('driver_schedules.driver_id', $driverId)
            ->get(['packages.*']);
    }

    public function findByAuthCode(string $authCodePackage): ?Package
    {
        return $this->entity
            ->where('auth_code', $authCodePackage)
            ->first();
    }

    public function findAllByDateAndOffice(int $officeId, Carbon $date): Collection
    {
        return $this->entity
            ->with(['pickupAddress', 'pickupAddress.country', 'arrivalAddress', 'arrivalAddress.country', 'request',
                'driverPackageSchedule', 'driverPackageSchedule.driverSchedule',
                'driverPackageSchedule.driverSchedule.driver'])
            ->join('driver_package_schedules AS dps','dps.package_id','=','packages.id')
            ->join('driver_schedules AS ds','dps.driver_schedule_id','=','ds.id')
            ->join('users', 'ds.driver_id', '=', 'users.id')
            ->whereDate('ds.start_date', $date)
            ->where('users.office_id', $officeId)
            ->get(['packages.*']);
    }
}
