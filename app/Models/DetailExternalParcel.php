<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailExternalParcel extends Model
{
    protected $primaryKey = 'package_id';

    protected $fillable = ['package_id', 'company_name', 'tracking_code', 'url_tracking'];

    protected $casts = [
        'package_id' => 'integer'
    ];
}
