<?php

namespace App\Models;

use App\Models\Contracts\ScopeFilterInterface;
use App\Models\Enums\NameRole;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model implements ScopeFilterInterface
{
    use Sortable;

    const INITIAL_CODE = 'SALA-';

    protected $fillable = ['code', 'name', 'office_id', 'no_people', 'recepcionist_id', 'status_id'];

    public $allowedSorts = ['code', 'name', 'no_people'];

    protected $casts = [
        'id' => 'integer',
        'office_id' => 'integer',
        'no_people' => 'integer',
        'recepcionist_id' => 'integer',
        'status_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopeFilter(Builder $query, array $params = []): Builder
    {
        if (empty($params)) {
            return $query;
        }

        if (isset($params['code']) && trim($params['code']) !== '') {
            $query->orWhere('code', 'LIKE', "%${params['code']}%");
        }
        if (isset($params['name']) && trim($params['name']) !== '') {
            $query->orWhere('name', 'LIKE', "%${params['name']}%");
        }
        if (isset($params['no_people'])) {
            $query->orWhere('no_people', $params['no_people']);
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

    public function recepcionist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recepcionist_id', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'status_id', 'id');
    }
}
