<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Office extends Model
{
    protected $fillable = ['name', 'state_id'];

    protected $casts = [
        'id' => 'integer',
        'state_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id', 'id');//Primeramente se pone llave foranea.
    }
}
