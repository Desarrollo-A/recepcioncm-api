<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Clase de viáticos
 */
class PerDiem extends Model
{
    protected $primaryKey = 'request_id';

    protected $fillable = ['request_id', 'gasoline', 'tollbooths', 'food', 'bill_filename', 'spent'];

    protected $casts = [
        'request_id' => 'integer',
        'gasoline' => 'float',
        'tollbooths' => 'float',
        'food' => 'float',
        'spent' => 'float'
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }
}
