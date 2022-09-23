<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    const RESTORE_PASSWORD_LENGTH = 15;

    protected $fillable = [
        'no_employee',
        'full_name',
        'email',
        'password',
        'personal_phone',
        'office_phone',
        'position',
        'area',
        'status',
        'role_id',
        'status_id',
        'office_id'
    ];

    protected $casts = [
        'id' => 'integer',
        'status_id' => 'integer',
        'role_id' => 'integer',
        'office_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'status_id', 'id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }
}
