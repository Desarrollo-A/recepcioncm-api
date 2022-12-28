<?php

use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\Office;
use App\Models\Address;
use App\Models\Lookup;
use App\Models\Enums\TypeLookup;
use App\Models\Enums\Lookups\CountryAddressLookup;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $qroState = State::query()->where('name', 'QRO')->first()->id;
        $leonState = State::query()->where('name', 'LEON')->first()->id;
        $slpState = State::query()->where('name', 'SLP')->first()->id;
        $cdmxState = State::query()->where('name', 'CDMX')->first()->id;
        $meridaState = State::query()->where('name', 'MERIDA')->first()->id;
        $cancunState = State::query()->where('name', 'CANCUN')->first()->id;
        $gdlState = State::query()->where('name', 'GDL')->first()->id;
        $tijuanaState = State::query()->where('name', 'TIJUANA')->first()->id;
        $smaState = State::query()->where('name', 'SAN MIGUEL DE ALLENDE')->first()->id;

        $countryMx = Lookup::query()
            ->where('type', TypeLookup::COUNTRY_ADDRESS)
            ->where('code', CountryAddressLookup::code(CountryAddressLookup::MEX))
            ->firstOrFail()
            ->id;

        $address = $this->createAddress('NA','NA','JURICA', '00000', 'QUERÉTARO',
            $countryMx);
        Office::query()->create(['name' => 'JURICA', 'state_id' => $qroState, 'address_id' => $address->id]);

        $address = $this->createAddress('5 DE MAYO','75','CENTRO HISTÓRICO', '76000',
            'QUERÉTARO', $countryMx);
        Office::query()->create(['name' => '5 DE MAYO', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('BLVD. BERNARDO QUINTANA','558','ARBOLEDAS', '00000',
            'QUERÉTARO', $countryMx, 'LOCAL B');
        Office::query()->create(['name' => 'BQ3', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('CERRO DE ACULTZINGO','302','COLINAS DEL CIMATARIO',
            '00000','QUERÉTARO', $countryMx);
        Office::query()->create(['name' => 'CIMATARIO', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('BLVD. BERNARDO QUINTANA','160','CARRETAS',
            '00000','QUERÉTARO', $countryMx, 'PLAZA BQ160');
        Office::query()->create(['name' => 'BQ1', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('CARR. 57 MEX-QRO. PARQUE INDUSTRIAL EUROBUSINESS PARK',
            'KM 201.5','SAN ISIDRO', '76240','EL MARQUÉS, QUERÉTARO', $countryMx, '109');
        Office::query()->create(['name' => 'OFICINA CONSTRUCCION', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('NA','NA','SANTA ROSA JAUREGUI',
            '00000','QUERÉTARO', $countryMx);
        Office::query()->create(['name' => 'NASCAA', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('VENUSTIANO CARRANZA','36','CENTRO',
            '76000','QUERÉTARO', $countryMx);
        Office::query()->create(['name' => 'CARRANZA QRO', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('VORTICE ITECH PARK','174','ANILLO VIAL III OTE.',
            '00000','SALDARRIAGA, QUERÉTARO', $countryMx);
        Office::query()->create(['name' => 'VORTICE', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('CARRETERA A HUIMILPAN','KM 11','EL ROSARIO',
            '76240','EL MARQUÉS, QUERÉTARO', $countryMx);
        Office::query()->create(['name' => 'TIERRA PARAISO', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('CALZ. DE LOS ARCOS','12','BOSQUES DEL ACUEDUCTO',
            '76020','QUERÉTARO', $countryMx);
        Office::query()->create(['name' => 'ARCOS', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('BOULEVARD BERNARDO QUINTANA','149','LOMA DORADA',
            '76060','QUERÉTARO', $countryMx);
        Office::query()->create(['name' => 'BQ2', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('PRIVADA AV. DE LAS TORRES','145','GALINDAS',
            '76117','QUERÉTARO', $countryMx);
        Office::query()->create(['name' => 'GALINDAS', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('NA','NA','EL MARQUES',
            '00000','QUERÉTARO', $countryMx);
        Office::query()->create(['name' => 'AMAZCALA', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('NA','NA','CONSTITUYENTES',
            '00000','QUERÉTARO', $countryMx);
        Office::query()->create(['name' => 'CONSTITUYENTES', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('5 DE MAYO','NA','NA',
            '00000','QUERÉTARO', $countryMx);
        Office::query()->create(['name' => 'QRO-VILLA', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('CARR. 57 MEX-QRO. PARQUE INDUSTRIAL EUROBUSINESS PARK',
            'KM 201.5','SAN ISIDRO','76240','EL MARQUÉS, QUERÉTARO', $countryMx, '100');
        Office::query()->create(['name' => 'OFICINA CONSTRUCCION 100', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('CARR. 57 MEX-QRO. PARQUE INDUSTRIAL EUROBUSINESS PARK',
            'NA','NA','00000','APASEO EL GRANDE, GUANUAJUATO', $countryMx);
        Office::query()->create(['name' => 'PETUNIAS', 'address_id' => $address->id, 'state_id' => $qroState]);

        $address = $this->createAddress('CARRETERA ESTATAL',
            '210-KM 1','LA PIEDAD','76245','QUERÉTARO', $countryMx);
        Office::query()->create(['name' => 'SENDAS', 'address_id' => $address->id, 'state_id' => $qroState]);


        $address = $this->createAddress('BLVD. LÓPEZ MATEOS',
            '101','NA','00000','LEÓN, GUANAJUATO', $countryMx, 'PB');
        Office::query()->create(['name' => 'TORRE BLANCA', 'address_id' => $address->id, 'state_id' => $leonState]);

        $address = $this->createAddress('PASEO DE LOS INSURGENTES',
            '1906','PANORAMA','00000','LEÓN, GUANAJUATO', $countryMx);
        Office::query()->create(['name' => 'INSURGENTES', 'address_id' => $address->id, 'state_id' => $leonState]);

        $address = $this->createAddress('AV. VENUSTIANO CARRANZA', '2425',
            'LOS FILTROS','00000','SAN LUIS POTOSÍ', $countryMx);
        Office::query()->create(['name' => 'CARRANZA SLP', 'address_id' => $address->id, 'state_id' => $slpState]);

        $address = $this->createAddress('AV. HOMERO', '906',
            'POLANCO II SECCIÓN, MIGUEL HIDALGO','11550','CDMX', $countryMx);
        Office::query()->create(['name' => 'POLANCO', 'address_id' => $address->id, 'state_id' => $cdmxState]);

        $address = $this->createAddress('CASA VILLA AURORA - CALLE 72', '342',
            'CENTRO','97000','MÉRIDA, YUCATÁN', $countryMx, 'ENTRE 33 Y 33-A');
        Office::query()->create(['name' => 'VILLA AURORA', 'address_id' => $address->id, 'state_id' => $meridaState]);

        $address = $this->createAddress('NA', 'NA',
            'NA','00000','CANCÚN, YUCATÁN', $countryMx);
        Office::query()->create(['name' => 'AMERICAS', 'address_id' => $address->id, 'state_id' => $cancunState]);

        $address = $this->createAddress('PLAZA MIDTOWN JALISCO', 'LOCAL 53-A PLANTA ALTA',
            'ITALIA PROVIDENCIA','00000','GUADALAJARA', $countryMx);
        Office::query()->create(['name' => 'MIDTOWN', 'address_id' => $address->id, 'state_id' => $gdlState]);

        $address = $this->createAddress('MISIÓN DE SAN JAVIER', '10643',
            'ZONA URBANA RIO TIJUANA','22010','TIJUANA, B.C', $countryMx);
        Office::query()->create(['name' => 'PLAZA SALINAS', 'address_id' => $address->id, 'state_id' => $tijuanaState]);

        $address = $this->createAddress('NA', 'NA',
            'NA','00000','GUANAJUATO', $countryMx);
        Office::query()->create(['name' => 'SMA', 'address_id' => $address->id, 'state_id' => $smaState]);
    }

    private function createAddress(string $street, string $numExt, string $suburb, string $postalCode, string $state,
                                   int $countryId, string $numInt = null): Address
    {
        return Address::create([
            'street' => $street,
            'num_ext' => $numExt,
            'num_int' => $numInt,
            'suburb' => $suburb,
            'postal_code' => $postalCode,
            'state' => $state,
            'country_id' => $countryId
        ]);
    }
}
