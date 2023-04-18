<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    protected $fillable = ['filename'];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}
