<?php

namespace App\Models;

use App\Models\Contracts\ScopeFilterInterface;
use App\Models\Enums\NameRole;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Car extends Model implements ScopeFilterInterface
{
    use Sortable;

    protected $fillable = ['business_name', 'trademark', 'model', 'color', 'license_plate', 'serie', 'circulation_card',
        'office_id', 'status_id', 'people'];

    public $allowedSorts = ['id', 'business_name', 'trademark', 'model', 'color', 'license_plate', 'serie', 'circulation_card',
        'people'];

    protected $casts = [
        'id' => 'integer',
        'office_id' => 'integer',
        'status_id' => 'integer',
        'people' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopeFilter(Builder $query, array $params = []): Builder
    {
        if (empty($params)) {
            return $query;
        }

        if (isset($params['business_name']) && trim($params['business_name'])) {
            $query->orWhere('business_name', 'LIKE', "%${params['business_name']}%");
        }
        if (isset($params['trademark']) && trim($params['trademark'])) {
            $query->orWhere('trademark', 'LIKE', "%${params['trademark']}%");
        }
        if (isset($params['model']) && trim($params['model'])) {
            $query->orWhere('model', 'LIKE', "%${params['model']}%");
        }
        if (isset($params['color']) && trim($params['color'])) {
            $query->orWhere('color', 'LIKE', "%${params['color']}%");
        }
        if (isset($params['license_plate']) && trim($params['license_plate'])) {
            $query->orWhere('license_plate', 'LIKE', "%${params['license_plate']}%");
        }
        if (isset($params['serie']) && trim($params['serie'])) {
            $query->orWhere('serie', 'LIKE', "%${params['serie']}%");
        }
        if (isset($params['circulation_card']) && trim($params['circulation_card'])) {
            $query->orWhere('circulation_card', 'LIKE', "%${params['circulation_card']}%");
        }
        if (isset($params['people'])) {
            $query->orWhere('people', $params['people']);
        }

        return $query;
    }

    public function scopeFilterOffice(Builder $query, User $user): Builder
    {
        if ($user->role->name === NameRole::RECEPCIONIST) {
            $query->where('office_id', $user->office_id);
        }

        return $query;
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'status_id', 'id');
    }

    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Car::class, 'car_driver', 'car_id')
            ->withTimestamps();
    }
}
