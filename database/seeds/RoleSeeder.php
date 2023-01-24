<?php

use App\Models\Enums\NameRole;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::query()->create(['name' => NameRole::ADMIN]);
        Role::query()->create(['name' => NameRole::RECEPCIONIST]);
        Role::query()->create(['name' => NameRole::APPLICANT]);
        Role::query()->create(['name' => NameRole::DRIVER]);
    }
}
