<?php

namespace App\Models;

use App\Models\Enums\Lookups\StatusRoomRequestLookup;
use App\Models\Enums\NameRole;
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
        'people', 'user_id', 'status_id', 'cancel_comment', 'event_google_calendar_id'];

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'type_id' => 'integer',
        'add_google_calendar' => 'boolean',
        'people' => 'integer',
        'user_id' => 'integer',
        'status_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'total' => 'integer',
        'weekday' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requestRoom(): HasOne
    {
        return $this->hasOne(RequestRoom::class);
    }

    public function package(): HasOne
    {
        return $this->hasOne(Package::class);
    }

    public function requestDriver(): HasOne
    {
        return $this->hasOne(RequestDriver::class);
    }

    public function requestCar(): HasOne
    {
        return $this->hasOne(RequestCar::class);
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

    public function requestEmail(): HasMany
    {
        return $this->hasMany(RequestEmail::class);
    }

    public function score(): HasOne
    {
        return $this->hasOne(Score::class);
    }

    public function perDiem(): HasOne
    {
        return $this->hasOne(PerDiem::class);
    }

    public function scopeExpired(Builder $query): Builder
    {
        $query->where('lookups.code', StatusRoomRequestLookup::code(StatusRoomRequestLookup::NEW));
        $query->orWhere('lookups.code', StatusRoomRequestLookup::code(StatusRoomRequestLookup::IN_REVIEW));
        return $query;
    }

    public function scopeFilterOfficeOrUser(Builder $query, User $user): Builder
    {
        if ($user->role->name === NameRole::RECEPCIONIST) {
            $query->where('request_room_view.office_id', $user->office_id);
        } else if ($user->role->name === NameRole::APPLICANT) {
            $query->where('request_room_view.user_id', $user->id);
        }

        return $query;
    }

    public function scopeJoinAllRecepcionist(Builder $query, int $officeId): Builder
    {
        return $query
            ->join('lookups AS s', 's.id', '=', 'requests.status_id')
            ->join('lookups AS t', 't.id', '=', 'requests.type_id')
            ->leftJoin('request_room AS rr', 'rr.request_id', '=', 'requests.id')
            ->leftJoin('rooms', 'rooms.id', '=', 'rr.room_id')
            ->leftJoin('packages AS p', 'p.request_id', '=', 'requests.id')
            ->leftJoin('request_drivers AS rd', 'rd.request_id', '=', 'requests.id')
            ->leftJoin('request_cars AS rc', 'rc.request_id', '=', 'requests.id')
            ->where(function (Builder $query) use ($officeId) {
                $query->where('rooms.office_id', $officeId)
                    ->orWhere('p.office_id', $officeId)
                    ->orWhere('rd.office_id', $officeId)
                    ->orWhere('rc.office_id', $officeId);
            });
    }
}
