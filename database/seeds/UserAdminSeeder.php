<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Lookup;
use App\Models\Office;
use App\Models\Enums\TypeLookup;
use App\Models\Enums\Lookups\StatusUserLookup;
use App\Models\Enums\NameRole;

class UserAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->create([
            'no_employee' => 'ADMIN',
            'full_name' => 'ADMINISTRADOR TI',
            'email' => 'desarrollo_apis@ciudadmaderas.com',
            'password' => bcrypt('tKx8Akw9Le3ZfDig'),
            'personal_phone' => '',
            'position' => 'ADMINISTRADOR',
            'area' => 'TI',
            'status_id' => Lookup::query()
                ->where('type', TypeLookup::STATUS_USER)
                ->where('code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
                ->first()
                ->id,
            'role_id' => Role::query()->where('name', NameRole::ADMIN)->first()->id
        ]);
    }
}
