<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeavyShipment extends Model
{
    protected $fillable = ['package_id', 'high', 'long', 'width', 'weight'];

    protected $casts = [
        'package_id' => 'integer',
        'high' => 'float',
        'long' => 'float',
        'width' => 'float',
        'weight' => 'float'
    ];
}
