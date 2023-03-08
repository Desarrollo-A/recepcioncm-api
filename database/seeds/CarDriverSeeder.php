<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Car;
use App\Models\Enums\NameRole;

class CarDriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleRecepcionist = Role::query()->where('name', NameRole::RECEPCIONIST)->first()->id;
        $roleDriver = Role::query()->where('name', NameRole::DRIVER)->first()->id;

        User::query()
            ->where('role_id', $roleRecepcionist)
            ->each(function (User $user) use ($roleDriver) {
                Car::query()
                    ->where('office_id', $user->office_id)
                    ->each(function (Car $car) use ($user, $roleDriver) {
                        $driver = User::query()
                            ->leftJoin('car_driver', 'car_driver.driver_id', '=', 'users.id')
                            ->where('users.office_id', $user->office_id)
                            ->where('users.role_id', $roleDriver)
                            ->whereNull('car_driver.car_id')
                            ->inRandomOrder()
                            ->firstOrFail();

                        $driver->cars()->sync(['car_id' => $car->id]);
                    });
            });
    }
}
