<?php

use Illuminate\Database\Seeder;
use App\Models\Lookup;
use App\Models\Role;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Enums\TypeLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\Lookups\InventoryTypeLookup;

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
                $papeleria = $types->first(function ($type) {
                    return $type->code === InventoryTypeLookup::code(InventoryTypeLookup::STATIONERY);
                });
                $botiquin = $types->first(function ($type) {
                    return $type->code === InventoryTypeLookup::code(InventoryTypeLookup::MEDICINE);
                });
                $limpieza = $types->first(function ($type) {
                    return $type->code === InventoryTypeLookup::code(InventoryTypeLookup::CLEANING);
                });
                $cafeteria = $types->first(function ($type) {
                    return $type->code === InventoryTypeLookup::code(InventoryTypeLookup::COFFEE);
                });

                Inventory::query()->create([
                    'name' => 'Lápiz #2',
                    'description' => 'Lapiz de madera  - paq con 12 pzas.',
                    'trademark' => 'Pencil',
                    'stock' => 20,
                    'minimum_stock' => 6,
                    'status' => true,
                    'image' => Inventory::IMAGE_DEFAULT,
                    'office_id' => $user->office_id,
                    'type_id' => $papeleria->id,
                    'unit_id' => $units->random()->id,
                    'meeting' => null
                ]);
                Inventory::query()->create([
                    'name' => 'Pluma Negra',
                    'description' => 'Pluma punto MEDIO  de 0.7 mm - caja con 12 pzas.',
                    'trademark' => 'Mapita',
                    'stock' => 10,
                    'minimum_stock' => 6,
                    'status' => true,
                    'image' => Inventory::IMAGE_DEFAULT,
                    'office_id' => $user->office_id,
                    'type_id' => $papeleria->id,
                    'unit_id' => $units->random()->id,
                    'meeting' => null
                ]);

                Inventory::query()->create([
                    'name' => 'Gasa',
                    'description' => 'Gasa absolvente esteril - 7.5 x 5 cm',
                    'trademark' => 'Le Roy',
                    'stock' => 5,
                    'minimum_stock' => 3,
                    'status' => true,
                    'image' => Inventory::IMAGE_DEFAULT,
                    'office_id' => $user->office_id,
                    'type_id' => $botiquin->id,
                    'unit_id' => $units->random()->id,
                    'meeting' => null
                ]);
                Inventory::query()->create([
                    'name' => 'Vitacilina',
                    'description' => 'Ungüento - 18 grms.',
                    'trademark' => 'Vitacilina',
                    'stock' => 2,
                    'minimum_stock' => 1,
                    'status' => true,
                    'image' => Inventory::IMAGE_DEFAULT,
                    'office_id' => $user->office_id,
                    'type_id' => $botiquin->id,
                    'unit_id' => $units->random()->id,
                    'meeting' => null
                ]);

                Inventory::query()->create([
                    'name' => 'Servilletas',
                    'description' => 'SANITAS INTERDOBLADAS',
                    'trademark' => 'Sanitas',
                    'stock' => 20,
                    'minimum_stock' => 10,
                    'status' => true,
                    'image' => Inventory::IMAGE_DEFAULT,
                    'office_id' => $user->office_id,
                    'type_id' => $limpieza->id,
                    'unit_id' => $units->random()->id,
                    'meeting' => null
                ]);
                Inventory::query()->create([
                    'name' => 'Papel Higiénico',
                    'description' => 'ROLLO DE PAPEL',
                    'trademark' => 'Desconocido',
                    'stock' => 10,
                    'minimum_stock' => 2,
                    'status' => true,
                    'image' => Inventory::IMAGE_DEFAULT,
                    'office_id' => $user->office_id,
                    'type_id' => $limpieza->id,
                    'unit_id' => $units->random()->id,
                    'meeting' => null
                ]);

                Inventory::query()->create([
                    'name' => 'Café de grano',
                    'description' => 'Café importado de Cuba',
                    'trademark' => 'Cubano',
                    'stock' => 8,
                    'minimum_stock' => 1,
                    'status' => true,
                    'image' => Inventory::IMAGE_DEFAULT,
                    'office_id' => $user->office_id,
                    'type_id' => $cafeteria->id,
                    'unit_id' => $units->random()->id,
                    'meeting' => null
                ]);
                Inventory::query()->create([
                    'name' => 'Coca de Lata',
                    'description' => 'COCA REGULAR 235 ML',
                    'trademark' => 'Coca Cola',
                    'stock' => 5,
                    'minimum_stock' => 1,
                    'status' => true,
                    'image' => Inventory::IMAGE_DEFAULT,
                    'office_id' => $user->office_id,
                    'type_id' => $cafeteria->id,
                    'unit_id' => $units->random()->id,
                    'meeting' => null
                ]);
                Inventory::query()->create([
                    'name' => 'Agua embotellada',
                    'description' => 'AGUA BONAFONT 235 ML',
                    'trademark' => 'Bonafont',
                    'stock' => 9,
                    'minimum_stock' => 5,
                    'status' => true,
                    'image' => Inventory::IMAGE_DEFAULT,
                    'office_id' => $user->office_id,
                    'type_id' => $cafeteria->id,
                    'unit_id' => $units->random()->id,
                    'meeting' => null
                ]);
            });
    }
}
