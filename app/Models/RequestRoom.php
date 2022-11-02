<?php

namespace App\Models;

use App\Models\Enums\Lookups\StatusRequestLookup;
use App\Models\Enums\NameRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequestRoom extends Model
{
    protected $table = 'request_room';
    protected $primaryKey = 'request_id';
    public $incrementing = false;

    protected $fillable = ['request_id', 'room_id', 'duration', 'external_people', 'level_id'];

    protected $casts = [
        'request_id' => 'integer',
        'duration' => 'integer',
        'room_id' => 'integer',
        'external_people' => 'integer',
        'level_id' => 'integer'
    ];

    public function getTotalRequestApprovedAttribute(): int
    {
        return Request::query()
            ->join('lookups', 'lookups.id', '=', 'requests.status_id')
            ->where('lookups.code', StatusRequestLookup::code(StatusRequestLookup::APPROVED))
            ->where('start_date', '>=', now()->startOfDay())
            ->where('user_id', $this->request->user_id)
            ->count();
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'level_id', 'id');
    }

    public function proposalRequest(): HasMany
    {
        return $this->hasMany(ProposalRequest::class, 'request_id', 'request_id');
    }

    public function scopeFilterOfficeOrUser(Builder $query, User $user): Builder
    {
        if ($user->role->name === NameRole::RECEPCIONIST) {
            $query->where('rooms.office_id', $user->office_id);
        } else if ($user->role->name === NameRole::APPLICANT) {
            $query->where('requests.user_id', $user->id);
        }

        return $query;
    }
}
