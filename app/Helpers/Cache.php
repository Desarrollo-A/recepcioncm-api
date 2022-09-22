<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Cache
{
    /**
     * @param string $key
     * @param Carbon $time
     * @param Collection|Model $data
     * @return mixed
     * @throws \Exception
     */
    public static function apply(string $key, Carbon $time, $data)
    {
        return cache()->remember($key, $time, function () use ($data) {
            return $data;
        });
    }

    /**
     * @param string $key
     * @return void
     * @throws \Exception
     */
    public static function forget(string $key)
    {
        cache()->forget($key);
    }

    public static function getKey(string $key): string
    {
        return strtoupper($key.'.KEY');
    }
}