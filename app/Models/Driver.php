<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = ['no_employee', 'full_name', 'email', 'personal_phone', 'office_phone', 'office_id',
        'status_id'];

    protected $casts = [
        'id' => 'integer',
        'office_id' => 'integer',
        'status_id' => 'integer'
    ];
}
