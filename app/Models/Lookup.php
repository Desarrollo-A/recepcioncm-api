<?php

namespace App\Models;

use App\Models\Contracts\ScopeFilterInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lookup extends Model implements ScopeFilterInterface
{
    protected $fillable = ['type', 'code', 'name'];

    protected $casts = [
        'id' => 'integer',
        'type' => 'integer',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'status_id', 'id');
    }

    public function scopeFilter(Builder $query, array $params = []): Builder
    {
        if (empty($params)) {
            return $query;
        }

        if (isset($params['lookup']) && trim($params['lookup']) !== '') {
            $query->where('name', 'LIKE', "%${params['lookup']}%");
        }

        return $query;
    }
}
