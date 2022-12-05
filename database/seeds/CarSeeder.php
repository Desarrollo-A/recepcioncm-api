<?php

use Illuminate\Database\Seeder;
use App\Models\Lookup;
use App\Models\Role;
use App\Models\User;
use App\Models\Car;
use App\Models\Enums\TypeLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\Lookups\StatusCarLookup;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = Lookup::query()
            ->where('type', TypeLookup::STATUS_CAR)
            ->where('code', StatusCarLookup::code(StatusCarLookup::ACTIVE))
            ->first()
            ->id;

        $roleRecepcionist = Role::query()->where('name', NameRole::RECEPCIONIST)->first()->id;

        User::query()
            ->where('role_id', $roleRecepcionist)
            ->get()
            ->each(function ($user) use ($status) {
                /*Car::query()->create([
                    'business_name' => 'Fraccionadora la Romita',
                    'trademark' => 'Chevrolet',
                    'model' => 'Beat 2018',
                    'color' => 'Blanco',
                    'license_plate' => 'UNT246A',
                    'serie' => 'MA6CA6CD3JT037782',
                    'circulation_card' => '1693814',
                    'people' => 4,
                    'office_id' => $user->office_id,
                    'status_id' => $status
                ]);*/
                factory(Car::class, 3)->create([
                    'office_id' => $user->office_id,
                    'status_id' => $status
                ]);
            });
    }
}
