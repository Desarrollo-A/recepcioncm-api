<?php

use Illuminate\Database\Seeder;
use App\Models\Enums\Lookups\StatusUserLookup;
use App\Models\Lookup;
use App\Models\Enums\TypeLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Enums\Lookups\StatusCarLookup;
use App\Models\Enums\Lookups\StatusRoomRequestLookup;
use App\Models\Enums\Lookups\LevelMeetingLookup;
use App\Models\Enums\Lookups\InventoryTypeLookup;
use App\Models\Enums\Lookups\UnitTypeLookup;
use App\Models\Enums\Lookups\StatusRoomLookup;
use App\Models\Enums\Lookups\TypeNotificationsLookup;
use App\Models\Enums\Lookups\NotificationColorLookup;
use App\Models\Enums\Lookups\NotificationIconLookup;
use App\Models\Enums\Lookups\ActionRequestNotificationLookup;
use App\Models\Enums\Lookups\StatusPackageRequestLookup;
use App\Models\Enums\Lookups\CountryAddressLookup;
use App\Models\Enums\Lookups\StatusDriverRequestLookup;
use App\Models\Enums\Lookups\StatusCarRequestLookup;

class LookupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StatusUserLookup::getAll()->each(function ($lookup) {
           $this->createLookup(TypeLookup::STATUS_USER, StatusUserLookup::code($lookup), $lookup);
        });

        TypeRequestLookup::getAll()->each(function ($lookup) {
            $this->createLookup(TypeLookup::TYPE_REQUEST, TypeRequestLookup::code($lookup), $lookup);
        });

        StatusRoomRequestLookup::getAll()->each(function ($lookup) {
            $this->createLookup(TypeLookup::STATUS_ROOM_REQUEST, StatusRoomRequestLookup::code($lookup), $lookup);
        });

        LevelMeetingLookup::getAll()->each(function ($lookup) {
            $this->createLookup(TypeLookup::LEVEL_MEETING, LevelMeetingLookup::code($lookup), $lookup);
        });

        InventoryTypeLookup::getAll()->each(function ($lookup) {
            $this->createLookup(TypeLookup::INVENTORY_TYPE, InventoryTypeLookup::code($lookup), $lookup);
        });

        UnitTypeLookup::getAll()->each(function ($lookup) {
            $this->createLookup(TypeLookup::UNIT_TYPE, UnitTypeLookup::code($lookup), $lookup);
        });

        StatusRoomLookup::getAll()->each(function ($lookup) {
            $this->createLookup(TypeLookup::STATUS_ROOM, StatusRoomLookup::code($lookup), $lookup);
        });

        TypeNotificationsLookup::getAll()->each(function ($lookup) {
            $this->createLookup(TypeLookup::REQUEST_TYPE_NOTIFICATIONS, TypeNotificationsLookup::code($lookup), $lookup);
        });

        StatusCarLookup::getAll()->each(function ($lookup) {
            $this->createLookup(TypeLookup::STATUS_CAR, StatusCarLookup::code($lookup), $lookup);
        });

        NotificationColorLookup::getAll()->each(function ($lookup) {
            $this->createLookup(TypeLookup::NOTIFICATION_COLOR, NotificationColorLookup::code($lookup), $lookup);
        });

        NotificationIconLookup::getAll()->each(function ($lookup) {
           $this->createLookup(TypeLookup::NOTIFICATION_ICON, NotificationIconLookup::code($lookup), $lookup);
        });

        ActionRequestNotificationLookup::getAll()->each(function ($lookup) {
            $this->createLookup(TypeLookup::ACTION_REQUEST_NOTIFICATION, ActionRequestNotificationLookup::code($lookup), $lookup);
        });
        
        StatusPackageRequestLookup::getAll()->each(function($lookup){
            $this->createLookup(TypeLookup::STATUS_PACKAGE_REQUEST, StatusPackageRequestLookup::code($lookup), $lookup);
        });

        CountryAddressLookup::getAll()->each(function (string $lookup) {
            $this->createLookup(TypeLookup::COUNTRY_ADDRESS, CountryAddressLookup::code($lookup), $lookup);
        });

        StatusDriverRequestLookup::getAll()->each(function (string $lookup) {
            $this->createLookup(TypeLookup::STATUS_DRIVER_REQUEST, StatusDriverRequestLookup::code($lookup), $lookup);
        });

        StatusCarRequestLookup::getAll()->each(function (string $lookup) {
            $this->createLookup(TypeLookup::STATUS_CAR_REQUEST, StatusCarRequestLookup::code($lookup), $lookup);
        });
    }

    private function createLookup(int $type, string $code, string $name): void
    {
        Lookup::query()->create([
            'type' => $type,
            'code' => $code,
            'name' => $name
        ]);
    }
}
