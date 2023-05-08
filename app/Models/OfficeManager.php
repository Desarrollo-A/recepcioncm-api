<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeManager extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'manager_id';

    protected $fillable = ['manager_id'];

    protected $casts = [
        'manager_id' => 'integer'
    ];
}
