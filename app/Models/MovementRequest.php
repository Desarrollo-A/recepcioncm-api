<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementRequest extends Model
{
    protected $fillable = ['request_id', 'user_id', 'description'];

    public $timestamps = false;

    protected $casts = [
        'request_id' => 'integer',
        'user_id' => 'integer'
    ];
}
