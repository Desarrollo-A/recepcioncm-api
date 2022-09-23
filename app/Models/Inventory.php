<?php

namespace App\Models;

use App\Models\Contracts\ScopeFilterInterface;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model implements ScopeFilterInterface
{
    use Sortable;

    const IMAGE_DEFAULT = 'no-image-inventory.png';

    protected $fillable = ['name', 'description', 'trademark', 'stock', 'minimum_stock', 'status', 'type_id', 'unit_id',
        'office_id', 'meeting', 'image'];

    public $allowedSorts = ['id', 'name', 'description', 'trademark', 'stock', 'minimum_stock', 'status_id'];

    protected $casts = [
        'id' => 'integer',
        'stock' => 'integer',
        'minimum_stock' => 'integer',
        'meeting' => 'integer',
        'status' => 'boolean',
        'type_id' => 'integer',
        'unit_id' => 'integer',
        'office_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopeFilter(Builder $query, array $params = []): Builder
    {
        if (empty($params)) {
            return $query;
        }

        if (isset($params['name']) && trim($params['name']) !== '') {
            $query->orWhere('name', 'LIKE', "%${params['name']}%");
        }
        if (isset($params['description']) && trim($params['description']) !== '') {
            $query->orWhere('description', 'LIKE', "%${params['description']}%");
        }
        if (isset($params['trademark']) && trim($params['trademark']) !== '') {
            $query->orWhere('trademark', 'LIKE', "%${params['trademark']}%");
        }
        if (isset($params['stock'])) {
            $query->orWhere('stock', $params['stock']);
        }
        if (isset($params['minimum_stock'])) {
            $query->orWhere('minimum_stock', $params['minimum_stock']);
        }

        return $query;
    }

    public function scopeFilterOffice(Builder $query, User $user): Builder
    {
        $query->where('office_id', $user->office_id);

        return $query;
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'type_id', 'id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'unit_id', 'id');
    }
}
