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
                factory(Car::class, 3)->create([
                    'office_id' => $user->office_id,
                    'status_id' => $status
                ]);
            });
    }
}
