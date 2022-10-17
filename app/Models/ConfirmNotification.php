<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfirmNotification extends Model
{
    protected $primaryKey = 'request_notification_id';
    protected $fillable = ['request_notification_id', 'is_answered'];
    protected $casts = [
        'request_notification_id' => 'integer',
        'is_answered' => 'boolean'
    ];
}
