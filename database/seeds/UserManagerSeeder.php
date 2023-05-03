<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\State;
use App\Models\Lookup;
use App\Models\Enums\TypeLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\Lookups\StatusUserLookup;

class UserManagerSeeder extends Seeder
{
    private $statusId;
    private $roleId;

    public function run(): void
    {
        $this->statusId = Lookup::query()
            ->where('type', TypeLookup::STATUS_USER)
            ->where('code', StatusUserLookup::code(StatusUserLookup::ACTIVE))
            ->first()
            ->id;

        $this->roleId = Role::query()
            ->where('name', NameRole::DEPARTMENT_MANAGER)
            ->first()
            ->id;

        $this->createUser(
            'HA00001',
            'MA YOLANDA VELAZQUEZ GUERRERO',
            'yolanda.velazquez@ciudadmaderas.com',
            '',
            '4422241061',
            'DIRECCION DE ADMINISTRACION',
            'ADMINISTRACION - ADMINISTRACION'
        );
        $this->createUser(
            'PF00001',
            'DIEGO IVAN ALVARADO ARELLANO',
            'diego.alvarado@ciudadmaderas.com',
            '',
            '4422241061',
            'DIRECCION DE CONTABILIDAD',
            'CONTABILIDAD - CONTABILIDAD'
        );
        $this->createUser(
            'RIGEL.SILVA',
            'RIGEL SILVA',
            'rigel.silva@ciudadmaderas.com',
            '',
            '4426884477',
            'DIRECCION DE COMERCIALIZACION',
            'COMERCIALIZACION - COMERCIALIZACION'
        );
        $this->createUser(
            'HA00007',
            'MARIELA SANCHEZ SANCHEZ',
            'mariela.sanchez@ciudadmaderas.com',
            '4423013879',
            '4429010507',
            'DIRECCION DE CONTRALORIA',
            'CONTRALORIA - CONTRALORIA'
        );
        $this->createUser(
            'HA00037',
            'MARTHA YVETTE ESTRADA MEJIA',
            'yvette.estrada@ciudadmaderas.com',
            '421220593',
            '4429010507',
            'DIRECCION DE MERCADOTECNIA',
            'MERCADOTECNIA - MERCADOTECNIA'
        );
        $this->createUser(
            'HA00008',
            'JORGE TORRES MACIAS',
            'jorge.torres@ciudadmaderas.com',
            '4424468131',
            '4429010507',
            'DIRECCION DE PROYECTOS',
            'PROYECTOS - PROYECTOS'
        );
        $this->createUser(
            'PF00002',
            'CONNIE SANCHEZ DIAZ',
            'connie@ciudadmaderas.com',
            '',
            '4429010507',
            'DIRECCION DE BIOFISICA APLICADA',
            'SCIO - QUANTUM BALANCE'
        );
        $this->createUser(
            'CIB02102',
            'MIRIAN OLVERA PERRUSQUIA',
            'asistente.juridicointerno@ciudadmaderas.com',
            '4481100215',
            '4429010507',
            'DIRECCION DE JURIDICO INTERNO',
            'JURIDICO INTERNO - JURIDICO INTERNO'
        );
        $this->createUser(
            'HP00002',
            'JULIO CESAR MOLINA VILLA',
            'julio.molina@ciudadmaderas.com',
            '4421602591',
            '4429010507',
            'DIRECCION DE FINANZAS',
            'FINANZAS - FINANZAS'
        );
        $this->createUser(
            'HA00002',
            'BARBARA ROJAS MUCIÑO',
            'barbara.rojas@ciudadmaderas.com',
            '4422395022',
            '4422281340',
            'DIRECCION DE COMPRAS',
            'COMPRAS - COMPRAS'
        );
        $this->createUser(
            'HA00029',
            'CINTHYA CRYSTAL LOPEZ VARGAS',
            'cinthya.lopez@ciudadmaderas.com',
            '5545219907',
            '4422281340',
            'DIRECCION DE CONTRATACION Y TITULACION',
            'CONTRATACION Y TITULACION - CONTRATACION Y TITULACION'
        );
        $this->createUser(
            'HA00016',
            'VICTOR EDUARDO ROSAS MACEDO',
            'victor.rosas@ciudadmaderas.com',
            '',
            '4423253428',
            'DIRECCION DE CONSTRUCCION',
            'ADMINISTRACION DE CONSTRUCCION - CONSTRUCCION'
        );
        $this->createUser(
            'HA00052',
            'ANDREA HERNANDEZ RESENDIZ',
            'andrea.hernandez@fundacionlamat.com.mx',
            '',
            '',
            'DIRECCION DE FUNDACIONES',
            'FUNDACIONES - FUNDACIONES'
        );
        $this->createUser(
            'HA00079',
            'REGINA ACOSTA MENDOZA',
            'regina.acosta@ciudadmaderas.com',
            '4422711373',
            '4422711373',
            'DIRECCION DE FUNDACION NASCAA',
            'NÄSCAA - FUNDACION NASCAA'
        );
        $this->createUser(
            'HA00031',
            'FABIAN ALEJANDRO SOTO ALONSO',
            'fabian.soto@ciudadmaderas.com',
            '4421438558',
            '4422248848',
            'DIRECCION DE TI',
            'TI - TI'
        );
        $this->createUser(
            'HA00110',
            'MARICELA RICO GODINEZ',
            'maricela.rico@ciudadmaderas.com',
            '4424672324',
            '4422248848',
            'DIRECCION DE CONTROL INTERNO',
            'CONTROL INTERNO - CONTROL INTERNO'
        );
        $this->createUser(
            'HA00072',
            'MARIO ENRIQUE ARREDONDO ESQUIVEL',
            'mario.arredondo@ciudadmaderas.com',
            '4423432189',
            '4425376464',
            'DIRECCION DE ARQUITECTURA DEL PAISAJE',
            'ARQUITECTURA DEL PAISAJE - ARQUITECTURA DEL PAISAJE'
        );
        $this->createUser(
            'HA00130',
            'HABIB ABRAHAM WEJEBE MOCTEZUMA',
            'habib.wejebe@ciudadmaderas.com',
            '4421103980',
            '4425376464',
            'DIRECCION DE OOAM',
            'OOAM - OOAM - ORGANISMO OPERADOR DE AGUAS MADERAS'
        );
        $this->createUser(
            'HA00022',
            'BLANCA ESTELA VALDERRABANO HERNANDEZ',
            'blanca.valderrabano@ciudadmaderas.com',
            '4427812320',
            '4426305669',
            'DIRECCION DE CAPITAL HUMANO',
            'CAPITAL HUMANO - CAPITAL HUMANO'
        );
        $this->createUser(
            'FRO01159',
            'LUCERITO VELAZQUEZ GUERRERO',
            'lucero.velazquez@ciudadmaderas.com',
            '',
            '4428268870',
            'DIRECCION DE COBRANZA',
            'COBRANZA - COBRANZA'
        );
        $this->createUser(
            'HA00005',
            'ADRIANA PATRICIA MAYA JEREZ',
            'patricia.maya@ciudadmaderas.com',
            '4423478647',
            '4428268870',
            'DIRECCION DE POST VENTA',
            'POST VENTA - POST VENTA'
        );
        $this->createUser(
            'HA00039',
            'LUCERITO VELAZQUEZ GUERRERO',
            'lucero.velazquez@ciudadmaderas.com',
            '',
            '4428268870',
            'DIRECCION DE COBRANZA',
            'COBRANZA - COBRANZA'
        );
        $this->createUser(
            'HA00100',
            'DANIELA BERENICE NAVARRO COVARRUBIAS',
            'daniela.navarro@ciudadmaderas.com',
            '4425920499',
            '4428268870',
            'DIRECCION DE COBRANZA GPH',
            'COBRANZA GPH - COBRANZA GPH'
        );
        $this->createUser(
            'HA00138',
            'MARIA XOCHITL AGUILAR MAQUEDA',
            'xochitl.aguilar@ciudadmaderas.com',
            '4422975189',
            '4422567890',
            'DIRECCION DE FUNDACION NYSSA',
            'NYSSA - FUNDACION NYSSA'
        );
    }

    private function createUser(string $noEmployee, string $fullName, string $email, string $personalPhone,
                                string $officePhone, string $position, string $area): void
    {
        User::query()->create([
            'no_employee' => $noEmployee,
            'full_name' => $fullName,
            'email' => $email,
            'password' => bcrypt('password'),
            'personal_phone' => $personalPhone,
            'office_phone' => $officePhone,
            'position' => $position,
            'area' => $area,
            'status_id' => $this->statusId,
            'role_id' => $this->roleId,
            'office_id' => null
        ]);
    }
}
