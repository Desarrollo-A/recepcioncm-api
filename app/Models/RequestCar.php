<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RequestCar extends Model
{
    protected $fillable = ['authorization_filename', 'responsive_filename', 'request_id', 'office_id', 'initial_km',
        'final_km', 'delivery_condition'];

    protected $casts = [
        'id' => 'integer',
        'request_id' => 'integer',
        'office_id' => 'integer',
        'initial_km' => 'integer',
        'final_km' => 'integer'
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function carRequestSchedule(): HasOne
    {
        return $this->hasOne(CarRequestSchedule::class);
    }
}
