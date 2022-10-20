<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Notification extends Model
{
    protected $fillable = ['message', 'user_id', 'type_id', 'is_read', 'color_id', 'icon_id'];

    protected $casts = [
        'id' => 'integer',
        'is_read' => 'boolean',
        'user_id' => 'integer',
        'type_id' => 'integer',
        'icon_id' => 'integer',
        'colorId' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'type_id', 'id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'color_id', 'id');
    }

    public function icon(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'icon_id', 'id');
    }

    public function requestNotification(): HasOne
    {
        return $this->hasOne(RequestNotification::class);
    }
}
