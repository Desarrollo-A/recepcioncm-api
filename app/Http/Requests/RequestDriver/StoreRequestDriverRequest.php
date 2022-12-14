<?php

namespace App\Http\Requests\RequestDriver;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\AddressDTO;
use App\Models\Dto\RequestDriverDTO;
use App\Models\Dto\RequestDTO;
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
            'startDate' => ['required', 'date', 'date_format:Y-m-d H:i', 'after:now'],
            'endDate' => ['required', 'date', 'date_format:Y-m-d H:i', 'after:startDate'],
            'people' => ['required', 'integer', 'min:2'],
            'comment' => ['nullable', 'string'],
            'addGoogleCalendar' => ['required', 'boolean'],

            'requestDriver.officeId' => ['required', 'integer']
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
            'requestDriver.pickupAddress.numExt' => 'N??mero exterior origen',
            'requestDriver.pickupAddress.numInt' => 'N??mero interior origen',
            'requestDriver.pickupAddress.suburb' => 'Colonia origen',
            'requestDriver.pickupAddress.postalCode' => 'C??digo postal origen',
            'requestDriver.pickupAddress.state' => 'Estado origen',
            'requestDriver.pickupAddress.countryId' => 'Pa??s origen',
            'requestDriver.pickupAddress.isExternal' => 'Direcci??n de salida externa',

            'requestDriver.arrivalAddress.street' => 'Calle destino',
            'requestDriver.arrivalAddress.numExt' => 'N??mero exterior destino',
            'requestDriver.arrivalAddress.numInt' => 'N??mero interior destino',
            'requestDriver.arrivalAddress.suburb' => 'Colonia destino',
            'requestDriver.arrivalAddress.postalCode' => 'C??digo postal destino',
            'requestDriver.arrivalAddress.state' => 'Estado destino',
            'requestDriver.arrivalAddress.countryId' => 'Pa??s destino',
            'requestDriver.arrivalAddress.isExternal' => 'Direcci??n de llegada externa',

            'title' => 'T??tulo',
            'startDate' => 'Fecha de salida',
            'endDate' => 'Fecha de llegada',
            'people' => 'N?? de personas',
            'comment' => 'Comentarios',
            'addGoogleCalendar' => 'A??adir a Google Calendar',

            'requestDriver.officeId' => 'Oficina',

            'requestDriver.pickupAddressId' =>  'ID direcci??n de salida',
            'requestDriver.arrivalAddressId' =>  'ID direcci??n llegada',
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestDriverDTO
    {
        $pickupAddressDTO = new AddressDTO([
            'street' => $this->requestDriver['pickupAddress']['street'],
            'num_ext' => $this->requestDriver['pickupAddress']['numExt'],
            'num_int' => $this->requestDriver['pickupAddress']['numInt'],
            'suburb' => $this->requestDriver['pickupAddress']['suburb'],
            'postal_code' => $this->requestDriver['pickupAddress']['postalCode'],
            'state' => $this->requestDriver['pickupAddress']['state'],
            'country_id' => $this->requestDriver['pickupAddress']['countryId']
        ]);

        $arrivalAddressDTO = new AddressDTO([
            'street' => $this->requestDriver['arrivalAddress']['street'],
            'num_ext' => $this->requestDriver['arrivalAddress']['numExt'],
            'num_int' => $this->requestDriver['arrivalAddress']['numInt'],
            'suburb' => $this->requestDriver['arrivalAddress']['suburb'],
            'postal_code' => $this->requestDriver['arrivalAddress']['postalCode'],
            'state' => $this->requestDriver['arrivalAddress']['state'],
            'country_id' => $this->requestDriver['arrivalAddress']['countryId']
        ]);

        $requestDTO = new RequestDTO([
            'title' => $this->title,
            'start_date' => new Carbon($this->startDate),
            'end_date' => new Carbon($this->endDate),
            'people' => $this->people,
            'comment' => $this->comment,
            'add_google_calendar' => $this->addGoogleCalendar,
            'user_id' => auth()->id()
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
