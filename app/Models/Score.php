<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $fillable = ['request_id', 'score', 'comment'];

    protected $casts = [
        'id' => 'integer',
        'request_id' => 'integer',
        'score' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
