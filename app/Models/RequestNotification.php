<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RequestNotification extends Model
{
    protected $fillable = ['notification_id', 'request_id'];

    protected $casts = [
        'notification_id' => 'integer',
        'request_id' => 'integer'
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function actionRequestNotification(): HasOne
    {
        return $this->hasOne(ActionRequestNotification::class);
    }
}
