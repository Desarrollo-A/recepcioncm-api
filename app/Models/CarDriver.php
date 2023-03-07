<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CarDriver extends Pivot
{
    protected $casts = [
        'driver_id' => 'integer',
        'car_id' => 'integer'
    ];
}
