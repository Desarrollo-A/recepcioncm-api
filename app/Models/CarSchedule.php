<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarSchedule extends Model
{
    protected $fillable = ['car_id', 'start_date', 'end_date'];

    protected $casts = [
        'id' => 'integer',
        'car_id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
