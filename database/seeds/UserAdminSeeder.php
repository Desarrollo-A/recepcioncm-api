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
            'no_employee' => 'CIB00000',
            'full_name' => 'ADMINISTRADOR TI',
            'email' => 'admin@ciudadmaderas.com',
            'password' => bcrypt('password'),
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

        User::query()->create([
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
        ]);
    }
}
