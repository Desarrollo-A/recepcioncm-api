<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ServiceLogicServiceProvider extends ServiceProvider
{
    const INTERFACE_SERVICE_NAMESPACE = 'App\Contracts\Services\\';
    const IMPLEMENT_SERVICE_NAMESPACE = 'App\Services\\';

    /**
     * @var array
     */
    protected $services = [];

    public function register()
    {
        foreach ($this->services as $interface => $implementation) {
            $this->app->bind(self::INTERFACE_SERVICE_NAMESPACE.$interface,
                self::IMPLEMENT_SERVICE_NAMESPACE.$implementation);
        }
    }

    public function boot()
    {
        //
    }
}