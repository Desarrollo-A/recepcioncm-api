<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = ['street', 'num_ext', 'num_int', 'suburb', 'postal_code', 'state', 'country_id'];

    protected $casts = [
        'id' => 'integer',
        'country_id' => 'integer'
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'country_id', 'id');
    }
}
