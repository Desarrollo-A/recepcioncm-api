<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Package extends Model
{
    protected $fillable = ['pickup_address_id', 'arrival_address_id', 'authorization_filename', 'name_receive', 'email_receive',
        'comment_receive', 'request_id', 'office_id', 'tracking_code'];

    protected $casts = [
        'id' => 'integer',
        'pickup_address_id' => 'integer',
        'arrival_address_id' => 'integer',
        'request_id' => 'integer',
        'office_id' => 'integer'
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function pickupAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'pickup_address_id');
    }

    public function arrivalAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'arrival_address_id');
    }
}