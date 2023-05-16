<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveredPackage extends Model
{
    protected $fillable = ['package_id', 'signature', 'name_receive', 'observations'];

    protected $primaryKey = 'package_id';

    protected $casts = [
        'package_id' => 'integer'
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
