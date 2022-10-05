<?php

namespace App\Models;

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
}
