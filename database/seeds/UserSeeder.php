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
    const TOTAL_USERS_APPLICANT = 7;
    const TOTAL_USERS_DRIVER = 5;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reception = Role::query()->where('name', NameRole::RECEPCIONIST)->first()->id;
        $solicitante = Role::query()->where('name', NameRole::APPLICANT)->first()->id;
        $conductor = Role::query()->where('name', NameRole::DRIVER)->first()->id;
        $officeId = Office::all(['id']);
        $activeStatus = Lookup::query()
            ->where('type', TypeLookup::STATUS_USER)
            ->where('code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
            ->first()
            ->id;

        $directors = User::query()
            ->whereHas('role', function (\Illuminate\Database\Eloquent\Builder $query) {
                $query->where('name', NameRole::DEPARTMENT_MANAGER);
            })
            ->get(['id']);

        for($i = 0; $i < 3; $i++) {
            factory(User::class)->create([
                'role_id' => $reception,
                'status_id' => $activeStatus,
                'office_id' => $officeId->random(),
                'department_manager_id' => $directors->random()
            ]);
        }

        factory(User::class, self::TOTAL_USERS_APPLICANT)->create([
            'role_id' => $solicitante,
            'status_id' => $activeStatus,
            'office_id' => $officeId->random(),
            'department_manager_id' => $directors->random()
        ]);

        User::query()
            ->where('role_id', $reception)
            ->get()
            ->each(function (User $user) use ($conductor, $activeStatus, $directors) {
                factory(User::class, self::TOTAL_USERS_DRIVER)->create([
                    'role_id' => $conductor,
                    'status_id' => $activeStatus,
                    'office_id' => $user->office_id,
                    'department_manager_id' => $directors->random()
                ]);
            });

        /*User::query()->create([
            'no_employee' => 'CIB02142',
            'full_name' => 'JORGE CORONEL GONZALEZ',
            'email' => 'programador.analista24@ciudadmaderas.com',
            'password' => bcrypt('password'),
            'personal_phone' => '4423178052',
            'office_phone' => '4422248848',
            'position' => 'PROGRAMADOR ANALISTA JR',
            'area' => 'TI - DESARROLLO',
            'status_id' => Lookup::query()
                ->where('type', TypeLookup::STATUS_USER)
                ->where('code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
                ->first()
                ->id,
            'role_id' => Role::query()->where('name', NameRole::APPLICANT)->first()->id,
            'office_id' => Office::query()->where('name', 'CARRANZA QRO')->first()->id
        ]);

        User::query()->create([
            'no_employee' => 'CIB01826',
            'full_name' => 'NANCY FERNANDA PIÑA GONZALEZ',
            'email' => 'recepcion.carranzaqro@ciudadmaderas.com',
            'password' => bcrypt('password'),
            'personal_phone' => '4427831495',
            'office_phone' => '4422248848',
            'position' => 'RECEPCIONISTA',
            'area' => 'TI - ADMINISTRACION DE OFICINA',
            'status_id' => Lookup::query()
                ->where('type', TypeLookup::STATUS_USER)
                ->where('code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
                ->first()
                ->id,
            'role_id' => Role::query()->where('name', NameRole::RECEPCIONIST)->first()->id,
            'office_id' => Office::query()->where('name', 'CARRANZA QRO')->first()->id
        ]);

        User::query()->create([
            'no_employee' => 'CIB00940',
            'full_name' => 'KELYN AMAIRANI HERNANDEZ RODRIGUEZ',
            'email' => 'programador.analista5@ciudadmaderas.com',
            'password' => bcrypt('password'),
            'personal_phone' => '0000000000',
            'office_phone' => '4422248848',
            'position' => 'PROGRAMADOR ANALISTA SR',
            'area' => 'TI - DESARROLLO',
            'status_id' => Lookup::query()
                ->where('type', TypeLookup::STATUS_USER)
                ->where('code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
                ->first()
                ->id,
            'role_id' => Role::query()->where('name', NameRole::APPLICANT)->first()->id,
            'office_id' => Office::query()->where('name', 'CARRANZA QRO')->first()->id
        ]);

        User::query()->create([
            'no_employee' => 'CIB01485',
            'full_name' => 'MARCIAL OZUNA CRISANTOS',
            'email' => 'programador.analista16@ciudadmaderas.com',
            'password' => bcrypt('password'),
            'personal_phone' => '7441303499',
            'office_phone' => '4422248848',
            'position' => 'PROGRAMADOR ANALISTA SR',
            'area' => 'TI - DESARROLLO',
            'status_id' => Lookup::query()
                ->where('type', TypeLookup::STATUS_USER)
                ->where('code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
                ->first()
                ->id,
            'role_id' => Role::query()->where('name', NameRole::APPLICANT)->first()->id,
            'office_id' => Office::query()->where('name', 'CARRANZA QRO')->first()->id
        ]);*/
    }
}
