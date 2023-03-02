<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalPackage extends Model
{
    protected $fillable = ['package_id', 'is_driver_selected'];

    public $timestamps = false;

    protected $primaryKey = 'package_id';

    protected $casts = [
        'package_id' => 'integer',
        'is_driver_selected' => 'boolean'
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
