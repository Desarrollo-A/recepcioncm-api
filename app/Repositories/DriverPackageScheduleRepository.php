<?php

namespace App\Repositories;

use App\Contracts\Repositories\DriverPackageScheduleRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\DriverPackageSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class DriverPackageScheduleRepository extends BaseRepository implements DriverPackageScheduleRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|DriverPackageSchedule
     */
    protected $entity;

    public function __construct(DriverPackageSchedule $entity)
    {
        $this->entity = $entity;
    }

    public function getScheduleDriverPackage(int $officeId): Collection
    {
        return $this->entity
            ->from('driver_package_schedules dps')
            ->join('driver_schedules ds','dps.driver_schedule_id','=','ds.id')
            ->join('drivers d','ds.driver_id','=','d.id')
            ->join('car_schedules cs','cs.car_id','=','dps.car_schedule_id')
            ->join('cars c','c.id','=','cs.car_id')
            ->where('d.office_id', $officeId)
            ->get(['ds.start_date', 'ds.end_date']);
    }

    public function deleteByPackageId(int $packageId): void
    {
        $this->entity
            ->where('package_id', $packageId)
            ->delete();
    }
}