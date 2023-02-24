<?php

namespace App\Models;

use App\Models\Contracts\ScopeFilterInterface;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Office extends Model implements ScopeFilterInterface
{
    use Sortable;

    protected $fillable = ['name', 'state_id', 'address_id'];

    public $allowedSorts = ['id', 'name'];

    protected $casts = [
        'id' => 'integer',
        'state_id' => 'integer',
        'address_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => 'boolean',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function scopeFilter(Builder $query, array $params = []): Builder
    {
        if (empty($params)) {
            return $query;
        }

        if (isset($params['name']) && trim($params['name']) !== '') {
            $query->orWhere('name', 'LIKE', "%${params['name']}%");
        }
        if (isset($params['state']) && trim($params['state']) !== '') {
            $query->orWhereHas('state', function (Builder $query) use ($params) {
                $query->where('name', 'LIKE', $params['state']);
            });
        }

        return $query;
    }
}
