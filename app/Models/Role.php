<?php

namespace App\Models;

use App\Models\Contracts\ScopeFilterInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Role extends Model implements ScopeFilterInterface
{
    protected $fillable = ['name', 'status'];

    protected $casts = [
        'id' => 'integer',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopeFilter(Builder $query, array $params = []): Builder
    {
        if (empty($params)) {
            return $query;
        }

        if (isset($params['role']) && trim($params['role']) !== '') {
            $query->where('name', 'LIKE', "%${params['role']}%");
        }

        return $query;
    }

    public function menus(): MorphToMany
    {
        return $this->morphedByMany(Menu::class, 'roleable');
    }

    public function submenus(): MorphToMany
    {
        return $this->morphedByMany(Submenu::class, 'roleable');
    }
}
