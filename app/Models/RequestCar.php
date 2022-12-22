<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RequestCar extends Model
{
    protected $fillable = ['authorization_filename', 'responsive_filename', 'request_id', 'office_id'];

    protected $casts = [
        'id' => 'integer',
        'request_id' => 'integer',
        'office_id' => 'integer'
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function carRequestSchedule(): HasOne
    {
        return $this->hasOne(CarRequestSchedule::class);
    }
}
