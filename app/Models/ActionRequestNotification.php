<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionRequestNotification extends Model
{
    protected $primaryKey = 'request_notification_id';
    protected $fillable = ['request_notification_id', 'is_answered', 'type_id'];
    protected $casts = [
        'request_notification_id' => 'integer',
        'is_answered' => 'boolean',
        'type_id' => 'integer'
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'type_id', 'id');
    }
}
