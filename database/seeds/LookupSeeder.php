<?php

use Illuminate\Database\Seeder;
use App\Models\Enums\Lookups\StatusUserLookup;
use App\Models\Lookup;
use App\Models\Enums\TypeLookup;
use App\Models\Enums\Lookups\ServicesListLookup;
use App\Models\Enums\Lookups\StatusCarLookup;
use App\Models\Enums\Lookups\StatusRequestLookup;
use App\Models\Enums\Lookups\LevelMeetingLookup;
use App\Models\Enums\Lookups\InventoryTypeLookup;
use App\Models\Enums\Lookups\UnitTypeLookup;
use App\Models\Enums\Lookups\StatusRoomLookup;
use App\Models\Enums\Lookups\TypeNotificationsLookup;
use App\Models\Enums\Lookups\NotificationColorLookup;

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
           Lookup::query()->create([
               'type' => TypeLookup::STATUS_USER,
               'code' => StatusUserLookup::code($lookup),
               'name' => $lookup
           ]);
        });

        ServicesListLookup::getAll()->each(function ($lookup) {
            Lookup::query()->create([
                'type' => TypeLookup::SERVICES_LIST,
                'code' => ServicesListLookup::code($lookup),
                'name' => $lookup
            ]);
        });

        StatusRequestLookup::getAll()->each(function ($lookup) {
            Lookup::query()->create([
                'type' => TypeLookup::STATUS_REQUEST,
                'code' => StatusRequestLookup::code($lookup),
                'name' => $lookup
            ]);
        });

        LevelMeetingLookup::getAll()->each(function ($lookup) {
            Lookup::query()->create([
                'type' => TypeLookup::LEVEL_MEETING,
                'code' => LevelMeetingLookup::code($lookup),
                'name' => $lookup
            ]);
        });

        InventoryTypeLookup::getAll()->each(function ($lookup) {
            Lookup::query()->create([
                'type' => TypeLookup::INVENTORY_TYPE,
                'code' => InventoryTypeLookup::code($lookup),
                'name' => $lookup
            ]);
        });

        UnitTypeLookup::getAll()->each(function ($lookup) {
            Lookup::query()->create([
                'type' => TypeLookup::UNIT_TYPE,
                'code' => UnitTypeLookup::code($lookup),
                'name' => $lookup
            ]);
        });

        StatusRoomLookup::getAll()->each(function ($lookup) {
            Lookup::query()->create([
                'type' => TypeLookup::STATUS_ROOM,
                'code' => StatusRoomLookup::code($lookup),
                'name' => $lookup
            ]);
        });

        TypeNotificationsLookup::getAll()->each(function ($lookup) {
            Lookup::query()->create([
                'type' => TypeLookup::REQUEST_TYPE_NOTIFICATIONS,
                'code' => TypeNotificationsLookup::code($lookup),
                'name' => $lookup
            ]);
        });

        StatusCarLookup::getAll()->each(function ($lookup) {
            Lookup::query()->create([
                'type' => TypeLookup::STATUS_CAR,
                'code' => StatusCarLookup::code($lookup),
                'name' => $lookup
            ]);
        });

        NotificationColorLookup::getAll()->each(function ($lookup) {
            Lookup::query()->create([
                'type' => TypeLookup::NOTIFICATION_COLOR,
                'code' => NotificationColorLookup::code($lookup),
                'name' => $lookup
            ]);
        });
    }
}
