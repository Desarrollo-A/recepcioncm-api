<?php

use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\Office;

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

        Office::query()->create(['name' => 'JURICA', 'state_id' => $qroState]);
        Office::query()->create([
            'name' => '5 DE MAYO',
            'address' => '5 DE MAYO NO. 75, CENTRO HISTÓRICO, C.P. 76000 QUERÉTARO, QRO.',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'BQ3',
            'address' => 'BLVD. BERNARDO QUINTANA 558, LOCAL B, COL. ARBOLEDAS, QUERÉTARO, QRO.',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'CIMATARIO',
            'address' => 'CERRO DE ACULTZINGO 302, COL. COLINAS DEL CIMATARIO, QUERÉTARO, QRO.',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'BQ1',
            'address' => 'BLVD. BERNARDO QUINTANA 160, PLAZA BQ160, COL. CARRETAS, QUERÉTARO, QRO.',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'OFICINA CONSTRUCCION',
            'address' => 'CARR. 57 MEX-QRO. KM 201.5 INT 109. PARQUE INDUSTRIAL EUROBUSINESS PARK COL. SAN ISIDRO CP. 76240 EL MARQUÉS QRO.',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'NASCAA',
            'address' => 'SANTA ROSA JAUREGUI, QRO.',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'CARRANZA QRO',
            'address' => 'CALLE VENUSTIANO CARRANZA #36 COLONIA CENTRO, QUERÉTARO, QRO. CP: 76000',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'VORTICE',
            'address' => 'VORTICE ITECH PARK #174 COL. ANILLO VIAL III OTE. SALDARRIAGA, QRO.',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'TIERRA PARAISO',
            'address' => 'CARRETERA A HUIMILPAN KM 11, EL ROSARIO, EL MARQUES, 76240 QRO.',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'ARCOS',
            'address' => 'CALZ. DE LOS ARCOS #12 COL. BOSQUES DEL ACUEDUCTO, C.P 76020 QUERÉTARO, QRO.',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'BQ2',
            'address' => 'BOULEVARD BERNARDO QUINTANA 149, COLONIA LOMA DORADA 76060, QUERÉTARO, QUERÉTARO',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'GALINDAS',
            'address' => 'PRIVADA AV. DE LAS TORRES 145, GALINDAS, 76177 SANTIAGO DE QUERÉTARO, QRO.',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'AMAZCALA',
            'address' => 'EL MARQUES QUERETARO',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'CONSTITUYENTES',
            'address' => 'CONSTITUYENTES',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'QRO-VILLA',
            'address' => '5 DE MAYO',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'OFICINA CONSTRUCCION 100',
            'address' => 'CARR. 57 MEX-QRO. KM 201.5 INT 100. PARQUE INDUSTRIAL EUROBUSINESS PARK COL. SAN ISIDRO CP. 76240 EL MARQUÉS QRO.',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'PETUNIAS',
            'address' => 'GRANJA "LA PETUNIA" COMUNIDAD PUNTA DE OBRAJUELO, APASEO EL GRANDE, GTO.',
            'state_id' => $qroState]);
        Office::query()->create([
            'name' => 'SENDAS',
            'address' => 'CARRETERA ESTATAL 210-KM 1, 76245 LA PIEDAD, QRO.',
            'state_id' => $qroState]);


        Office::query()->create([
            'name' => 'TORRE BLANCA',
            'address' => 'BLVD. LÓPEZ MATEOS #101 PB, ESQ. VASCO DE QUIROGA, LEÓN, GTO.',
            'state_id' => $leonState]);
        Office::query()->create([
            'name' => 'INSURGENTES',
            'address' => 'PASEO DE LOS INSURGENTES # 1906, COL. PANORAMA',
            'state_id' => $leonState]);

        Office::query()->create([
            'name' => 'CARRANZA SLP',
            'address' => 'AV. VENUSTIANO CARRANZA 2425, COL. LOS FILTROS, SAN LUIS POTOSÍ.',
            'state_id' => $slpState]);

        Office::query()->create([
            'name' => 'POLANCO',
            'address' => 'AV. HOMERO 906, POLANCO II SECCIóN, MIGUEL HIDALGO, C.P. 11550, CDMX',
            'state_id' => $cdmxState]);

        Office::query()->create([
            'name' => 'VILLA AURORA',
            'address' => 'CASA VILLA AURORA - CALLE 72 NO. 342 ENTRE 33 Y 33-A, COL. CENTRO, C.P. 97000, MÉRIDA, YUCATÁN.',
            'state_id' => $meridaState]);

        Office::query()->create([
            'name' => 'AMERICAS',
            'address' => 'CANCUN',
            'state_id' => $cancunState]);

        Office::query()->create([
            'name' => 'MIDTOWN',
            'address' => 'PLAZA MIDTOWN JALISCO, LOCAL 53-A PLANTA ALTA, ITALIA PROVIDENCIA',
            'state_id' => $gdlState]);

        Office::query()->create([
            'name' => 'PLAZA SALINAS',
            'address' => 'MISIÓN DE SAN JAVIER 10643 ZONA URBANA RIO TIJUANA, 22010 TIJUANA, B.C',
            'state_id' => $tijuanaState]);

        Office::query()->create([
            'name' => 'SMA',
            'address' => 'GUANAJUATO',
            'state_id' => $smaState]);
    }
}
