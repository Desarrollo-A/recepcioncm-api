<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestEmail extends Model
{
    protected $fillable = ['request_id', 'name', 'email'];

    protected $casts = [
        'id' => 'integer',
        'request_id' => 'integer'
    ];
}
