<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalRequest extends Model
{
    protected $fillable = ['request_id', 'start_date', 'end_date'];

    public $timestamps = false;

    protected $casts = [
        'request_id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];
}
