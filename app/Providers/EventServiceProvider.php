<?php

namespace App\Providers;

use App\Models\Inventory;
use App\Models\InventoryRequest;
use App\Models\Package;
use App\Models\Request;
use App\Models\RequestCar;
use App\Models\RequestDriver;
use App\Models\RequestRoom;
use App\Models\Room;
use App\Observers\InventoryObserver;
use App\Observers\InventoryRequestObserver;
use App\Observers\PackageObserver;
use App\Observers\RequestCarObserver;
use App\Observers\RequestDriverObserver;
use App\Observers\RequestObserver;
use App\Observers\RequestRoomObserver;
use App\Observers\RoomObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Inventory::observe(InventoryObserver::class);
        Room::observe(RoomObserver::class);
        InventoryRequest::observe(InventoryRequestObserver::class);
        RequestRoom::observe(RequestRoomObserver::class);
        Request::observe(RequestObserver::class);
        Package::observe(PackageObserver::class);
        RequestDriver::observe(RequestDriverObserver::class);
        RequestCar::observe(RequestCarObserver::class);
    }
}
