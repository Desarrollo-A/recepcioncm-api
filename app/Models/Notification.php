<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = ['message', 'user_id', 'request_id', 'type_id', 'is_read', 'color_id'];

    public $allowedSorts = ['id'];

    protected $casts = [
        'id' => 'integer',
        'is_read' => 'boolean',
        'user_id' => 'integer',
        'request_id' => 'integer',
        'type_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'type_id', 'id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'color_id', 'id');
    }
}
