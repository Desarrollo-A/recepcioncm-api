<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverPackageSchedule extends Model
{
    protected $fillable = ['package_id', 'driver_schedule_id', 'car_schedule_id'];

    protected $casts = [
        'package_id' => 'integer',
        'driver_schedule_id' => 'integer',
        'car_schedule_id' => 'integer'
    ];

    public function carSchedule(): BelongsTo
    {
        return $this->belongsTo(CarSchedule::class);
    }

    public function driverSchedule(): BelongsTo
    {
        return $this->belongsTo(DriverSchedule::class);
    }
}
