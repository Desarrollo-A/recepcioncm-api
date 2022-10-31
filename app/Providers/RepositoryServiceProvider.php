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
        'UserRepositoryInterface' => 'UserRepository',
        'MenuRepositoryInterface' => 'MenuRepository',
        'SubmenuRepositoryInterface' => 'SubmenuRepository',
        'LookupRepositoryInterface' => 'LookupRepository',
        'RoleRepositoryInterface' => 'RoleRepository',
        'OfficeRepositoryInterface' => 'OfficeRepository',
        'RoomRepositoryInterface' => 'RoomRepository',
        'StateRepositoryInterface' => 'StateRepository',
        'RequestRepositoryInterface' => 'RequestRepository',
        'RequestRoomRepositoryInterface' => 'RequestRoomRepository',
        'InventoryRepositoryInterface' => 'InventoryRepository',
        'NotificationRepositoryInterface' => 'NotificationRepository',
        'CarRepositoryInterface' => 'CarRepository',
        'RequestRoomViewRepositoryInterface' => 'RequestRoomViewRepository',
        'InventoryHistoryRepositoryInterface' => 'InventoryHistoryRepository',
        'InventoryRequestRepositoryInterface' => 'InventoryRequestRepository',
        'CancelRequestRepositoryInterface' => 'CancelRequestRepository',
        'ProposalRequestRepositoryInterface' => 'ProposalRequestRepository',
        'RequestPhoneNumberRepositoryInterface' => 'RequestPhoneNumberRepository',
        'ConfirmNotificationRepositoryInterface' => 'ConfirmNotificationRepository',
        'RequestNotificationRepositoryInterface' => 'RequestNotificationRepository',
        'InputOutputInventoryViewRepositoryInterface' => 'InputOutputInventoryViewRepository',
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