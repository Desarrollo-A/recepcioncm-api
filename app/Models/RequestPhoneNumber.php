<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestPhoneNumber extends Model
{
    protected $fillable = ['request_id', 'name', 'phone', 'created_at', 'updated_at'];

    protected $casts = [
        'id' => 'integer',
        'request_id' => 'integer'
    ];
}
