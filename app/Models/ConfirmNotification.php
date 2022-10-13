<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfirmNotification extends Model
{
    protected $fillable = ['notification_id', 'is_answered'];
    protected $casts = [
        'notification_id' => 'integer',
        'is_answered' => 'boolean'
    ];
}
