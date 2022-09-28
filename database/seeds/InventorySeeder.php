<?php

use Illuminate\Database\Seeder;
use App\Models\Lookup;
use App\Models\Role;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Enums\TypeLookup;
use App\Models\Enums\NameRole;

class InventorySeeder extends Seeder
{
    const INVENTORY_BY_OFFICE = 50;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = Lookup::query()
            ->where('type', TypeLookup::INVENTORY_TYPE)
            ->get();
        $units = Lookup::query()
            ->where('type', TypeLookup::UNIT_TYPE)
            ->get();

        $roleRecepcionist = Role::query()->where('name', NameRole::RECEPCIONIST)->first()->id;

        User::query()
            ->where('role_id', $roleRecepcionist)
            ->get()
            ->each(function ($user) use ($types, $units) {
                $types->each(function ($type) use ($units, $user) {
                    for ($i = 0; $i < self::INVENTORY_BY_OFFICE; $i++) {
                        $meeting = (rand(0,1)) ? rand(2,5) : null;

                        factory(Inventory::class)
                            ->create([
                                'office_id' => $user->office_id,
                                'type_id' => $type->id,
                                'unit_id' => $units->random()->id,
                                'meeting' => $meeting
                            ]);
                    }
                });
            });
    }
}
