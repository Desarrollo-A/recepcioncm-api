<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverPackageSchedule extends Model
{
    protected $fillable = ['package_id', 'driver_schedule_id', 'car_schedule_id'];

    protected $casts = [
        'package_id' => 'integer',
        'driver_schedule_id' => 'integer',
        'car_schedule_id' => 'integer'
    ];
}
