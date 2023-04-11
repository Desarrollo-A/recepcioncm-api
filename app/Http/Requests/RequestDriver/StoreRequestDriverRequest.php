<?php

namespace App\Http\Requests\RequestDriver;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\AddressDTO;
use App\Models\Dto\RequestDriverDTO;
use App\Models\Dto\RequestDTO;
use App\Models\Dto\RequestEmailDTO;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequestDriverRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rulesArray = [
            'requestDriver.pickupAddress.isExternal' => ['required', 'boolean', 'bail'],

            'requestDriver.arrivalAddress.isExternal' => ['required', 'boolean', 'bail'],

            'title' => ['required', 'string', 'min:3', 'max: 100'],
            'startDate' => ['required', 'date', 'date_format:Y-m-d H:i:s', 'after:now'],
            'endDate' => ['required', 'date', 'date_format:Y-m-d H:i:s', 'after:startDate'],
            'people' => ['required', 'integer', 'min:2'],
            'comment' => ['nullable', 'string'],
            'addGoogleCalendar' => ['required', 'boolean'],

            'requestDriver.officeId' => ['required', 'integer'],

            'requestEmail' => ['array'],
            'requestEmail.*.name' => ['required', 'max:150'],
            'requestEmail.*.email' => ['required', 'email:dns', 'max:150'],
        ];

        if ($this->requestDriver['pickupAddress']['isExternal']) {
            $rulesArray = array_merge($rulesArray, [
                'requestDriver.pickupAddress.street' => ['required', 'min:5', 'max:150'],
                'requestDriver.pickupAddress.numExt' => ['required', 'min:1', 'max:50'],
                'requestDriver.pickupAddress.numInt' => ['nullable', 'min:1', 'max:50'],
                'requestDriver.pickupAddress.suburb' => ['required', 'min:5', 'max:120'],
                'requestDriver.pickupAddress.postalCode' => ['required', 'min:3', 'max:25'],
                'requestDriver.pickupAddress.state' => ['min:3', 'max:100'],
                'requestDriver.pickupAddress.countryId' => ['required', 'integer'],
            ]);
        }else {
            $rulesArray = array_merge($rulesArray, [
                'requestDriver.pickupAddressId' => ['required', 'integer']
            ]);
        }

        if ($this->requestDriver['arrivalAddress']['isExternal']) {
            $rulesArray = array_merge($rulesArray, [
                
                'requestDriver.arrivalAddress.street' => ['required', 'min:5', 'max:150'],
                'requestDriver.arrivalAddress.numExt' => ['required', 'min:1', 'max:50'],
                'requestDriver.arrivalAddress.numInt' => ['nullable', 'min:1', 'max:50'],
                'requestDriver.arrivalAddress.suburb' => ['min:5', 'max:120'],
                'requestDriver.arrivalAddress.postalCode' => ['min:3', 'max:25'],
                'requestDriver.arrivalAddress.state' => ['min:3', 'max:100'],
                'requestDriver.arrivalAddress.countryId' => ['required', 'integer'],
            ]);
        }else {
            $rulesArray = array_merge($rulesArray, [
                'requestDriver.arrivalAddressId' => ['required', 'integer']
            ]);
        }

        return $rulesArray;
    }
    
    public function attributes(): array
    {
        return [
            'requestDriver.pickupAddress.street' => 'Calle origen',
            'requestDriver.pickupAddress.numExt' => 'Número exterior origen',
            'requestDriver.pickupAddress.numInt' => 'Número interior origen',
            'requestDriver.pickupAddress.suburb' => 'Colonia origen',
            'requestDriver.pickupAddress.postalCode' => 'Código postal origen',
            'requestDriver.pickupAddress.state' => 'Estado origen',
            'requestDriver.pickupAddress.countryId' => 'País origen',
            'requestDriver.pickupAddress.isExternal' => 'Dirección de salida externa',

            'requestDriver.arrivalAddress.street' => 'Calle destino',
            'requestDriver.arrivalAddress.numExt' => 'Número exterior destino',
            'requestDriver.arrivalAddress.numInt' => 'Número interior destino',
            'requestDriver.arrivalAddress.suburb' => 'Colonia destino',
            'requestDriver.arrivalAddress.postalCode' => 'Código postal destino',
            'requestDriver.arrivalAddress.state' => 'Estado destino',
            'requestDriver.arrivalAddress.countryId' => 'País destino',
            'requestDriver.arrivalAddress.isExternal' => 'Dirección de llegada externa',

            'title' => 'Título',
            'startDate' => 'Fecha de salida',
            'endDate' => 'Fecha de llegada',
            'people' => 'N° de personas',
            'comment' => 'Comentarios',
            'addGoogleCalendar' => 'Añadir a Google Calendar',

            'requestDriver.officeId' => 'Oficina',

            'requestDriver.pickupAddressId' =>  'ID dirección de salida',
            'requestDriver.arrivalAddressId' =>  'ID dirección llegada',

            'requestEmail' => 'Listado de correos electrónicos',
            'requestEmail.*.name' => 'Nombre del contacto',
            'requestEmail.*.email' => 'Correo del contacto',
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestDriverDTO
    {
        $now = now();
        $emails = array();
        foreach ($this->requestEmail as $email) {
            $emails[] = new RequestEmailDTO([
                'name' => trim($email['name']),
                'email' => trim($email['email']),
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        $pickupAddressDTO = new AddressDTO([
            'street' => trim($this->requestDriver['pickupAddress']['street']),
            'num_ext' => trim($this->requestDriver['pickupAddress']['numExt']),
            'num_int' => trim($this->requestDriver['pickupAddress']['numInt']),
            'suburb' => trim($this->requestDriver['pickupAddress']['suburb']),
            'postal_code' => trim($this->requestDriver['pickupAddress']['postalCode']),
            'state' => trim($this->requestDriver['pickupAddress']['state']),
            'country_id' => $this->requestDriver['pickupAddress']['countryId']
        ]);

        $arrivalAddressDTO = new AddressDTO([
            'street' => trim($this->requestDriver['arrivalAddress']['street']),
            'num_ext' => trim($this->requestDriver['arrivalAddress']['numExt']),
            'num_int' => trim($this->requestDriver['arrivalAddress']['numInt']),
            'suburb' => trim($this->requestDriver['arrivalAddress']['suburb']),
            'postal_code' => trim($this->requestDriver['arrivalAddress']['postalCode']),
            'state' => trim($this->requestDriver['arrivalAddress']['state']),
            'country_id' => $this->requestDriver['arrivalAddress']['countryId']
        ]);

        $requestDTO = new RequestDTO([
            'title' => $this->title,
            'start_date' => new Carbon($this->startDate),
            'end_date' => new Carbon($this->endDate),
            'people' => $this->people,
            'comment' => trim($this->comment),
            'add_google_calendar' => $this->addGoogleCalendar,
            'user_id' => auth()->id(),
            'requestEmail' => $emails
        ]);
        
        return new RequestDriverDTO([
            'request' => $requestDTO,
            'pickupAddress' => $pickupAddressDTO,
            'arrivalAddress' => $arrivalAddressDTO,
            'office_id' => $this->requestDriver['officeId'],
            'pickup_address_id' => $this->requestDriver['pickupAddressId'],
            'arrival_address_id' => $this->requestDriver['arrivalAddressId']
        ]);
    }
}
