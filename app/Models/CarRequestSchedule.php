<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarRequestSchedule extends Model
{
    protected $fillable = ['request_car_id', 'car_schedule_id'];

    protected $casts = [
        'request_car_id' => 'integer',
        'car_schedule_id' => 'integer'
    ];

    public function carSchedule(): BelongsTo
    {
        return $this->belongsTo(CarSchedule::class);
    }
}
