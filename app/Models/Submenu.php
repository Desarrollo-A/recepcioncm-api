<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Submenu extends Model
{
    protected $fillable = ['path_route', 'label', 'order', 'menu_id', 'status'];

    protected $casts = [
        'id' => 'integer',
        'order' => 'integer',
        'menu_id' => 'integer',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
