<?php

namespace App\Models;

use App\Models\Enums\Lookups\StatusRequestLookup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Request extends Model
{
    const INITIAL_CODE = 'SOL-';

    protected $fillable = ['code', 'title', 'start_date', 'end_date', 'type_id', 'comment', 'add_google_calendar',
        'people', 'user_id', 'status_id', 'cancel_comment'];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'type_id' => 'integer',
        'add_google_calendar' => 'boolean',
        'people' => 'integer',
        'user_id' => 'integer',
        'status_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requestRoom(): HasOne
    {
        return $this->hasOne(RequestRoom::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'status_id', 'id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'type_id', 'id');
    }

    public function inventories(): BelongsToMany
    {
        return $this->belongsToMany(Inventory::class, 'inventory_request')
            ->withPivot('inventory_id', 'quantity', 'applied', 'created_at', 'updated_at')
            ->using(InventoryRequest::class);
    }

    public function cancelRequest(): HasOne
    {
        return $this->hasOne(CancelRequest::class);
    }

    public function proposalRequest(): HasMany
    {
        return $this->hasMany(ProposalRequest::class);
    }

    public function requestPhoneNumber(): HasMany
    {
        return $this->hasMany(RequestPhoneNumber::class);
    }

    public function scopeExpired(Builder $query): Builder
    {
        $query->orWhere('lookups.code', StatusRequestLookup::code(StatusRequestLookup::NEW));
        $query->orWhere('lookups.code', StatusRequestLookup::code(StatusRequestLookup::IN_REVIEW));
        return $query;
    }
}
