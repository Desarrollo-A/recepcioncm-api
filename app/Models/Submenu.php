<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Submenu extends Model
{
    protected $fillable = ['path_route', 'label', 'order', 'menu_id'];

    protected $casts = [
        'id' => 'integer',
        'order' => 'integer',
        'menu_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
