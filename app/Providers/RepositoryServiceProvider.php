<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    const INTERFACE_REPOSITORY_NAMESPACE = 'App\Contracts\Repositories\\';
    const IMPLEMENT_REPOSITORY_NAMESPACE = 'App\Repositories\\';

    /**
     * @var array
     */
    protected $repositories = [
        'UserRepositoryInterface' => 'UserRepository'
    ];

    public function register()
    {
        foreach ($this->repositories as $interface => $implementation) {
            $this->app->bind(self::INTERFACE_REPOSITORY_NAMESPACE.$interface,
                self::IMPLEMENT_REPOSITORY_NAMESPACE.$implementation);
        }
    }

    public function boot()
    {
        //
    }
}