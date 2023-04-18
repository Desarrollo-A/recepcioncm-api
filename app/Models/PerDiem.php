<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Clase de viáticos
 */
class PerDiem extends Model
{
    protected $fillable = ['request_id', 'gasoline', 'tollbooths', 'food', 'spent'];

    protected $casts = [
        'id' => 'integer',
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

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
