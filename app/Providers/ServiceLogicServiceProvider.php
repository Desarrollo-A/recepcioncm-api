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
    protected $services = [
        'AuthServiceInterface' => 'AuthService',
        'UserServiceInterface' => 'UserService',
        'MenuServiceInterface' => 'MenuService',
        'RoomServiceInterface' => 'RoomService',
        'LookupServiceInterface' => 'LookupService',
        'StateServiceInterface' => 'StateService',
        'RequestRoomServiceInterface' => 'RequestRoomService',
        'InventoryServiceInterface' => 'InventoryService',
        'CarServiceInterface' => 'CarService',
        'InventoryHistoryServiceInterface' => 'InventoryHistoryService',
        'NotificationServiceInterface' => 'NotificationService',
        'InventoryRequestServiceInterface' => 'InventoryRequestService',
        'RequestServiceInterface' => 'RequestService',
        'CalendarServiceInterface' => 'CalendarService',
        'RequestPhoneNumberServiceInterface' => 'RequestPhoneNumberService',
        'ActionRequestNotificationServiceInterface' => 'ActionRequestNotificationService',
        'RequestNotificationServiceInterface' => 'RequestNotificationService',
        'HomeServiceInterface' => 'HomeService',
        'InputOutputInventoryViewServiceInterface' => 'InputOutputInventoryViewService',
        'RequestEmailServiceInterface' => 'RequestEmailService',
        'ScoreServiceInterface' => 'ScoreService',
        'OfficeServiceInterface'    =>  'OfficeService',
    ];

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