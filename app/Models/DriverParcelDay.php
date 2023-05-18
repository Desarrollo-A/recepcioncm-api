<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverParcelDay extends Model
{
    public $timestamps = false;

    protected $fillable = ['driver_id', 'day_id'];

    protected $casts = [
        'driver_id' => 'integer',
        'day_id' => 'integer'
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'day_id');
    }
}
