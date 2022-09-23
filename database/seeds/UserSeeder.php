<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Office;
use App\Models\Lookup;
use App\Models\User;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use App\Models\Enums\Lookups\StatusUserLookup;

class UserSeeder extends Seeder
{
    const TOTAL_USERS_APPLICANT = 15;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reception = Role::query()->where('name', NameRole::RECEPCIONIST)->first()->id;
        $solicitante = Role::query()->where('name', NameRole::APPLICANT)->first()->id;
        $officeId = Office::all(['id']);
        $activeStatus = Lookup::query()
            ->where('type', TypeLookup::STATUS_USER)
            ->where('code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
            ->first()
            ->id;

        for($i = 0; $i < 3; $i++) {
            factory(User::class)->create([
                'role_id' => $reception,
                'status_id' => $activeStatus,
                'office_id' => $officeId->random()
            ]);
        }

        factory(User::class, self::TOTAL_USERS_APPLICANT)->create([
            'role_id' => $solicitante,
            'status_id' => $activeStatus,
            'office_id' => $officeId->random()
        ]);
    }
}
