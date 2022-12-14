<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverSchedule extends Model
{
    protected $fillable = ['driver_id', 'start_date', 'end_date'];

    protected $casts = [
        'id' => 'integer',
        'driver_id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
