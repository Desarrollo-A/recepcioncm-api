<?php

namespace App\Http\Requests\RequestPackage;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\AddressDTO;
use App\Models\Dto\PackageDTO;
use App\Models\Dto\RequestDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequestPackageRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'package.pickupAddress.street' => ['required', 'min:5', 'max:150'],
            'package.pickupAddress.numExt' => ['required', 'min:1', 'max:50'],
            'package.pickupAddress.numInt' => ['nullable', 'min:1', 'max:50'],
            'package.pickupAddress.suburb' => ['required', 'min:5', 'max:120'],
            'package.pickupAddress.postalCode' => ['required', 'min:3', 'max:25'],
            'package.pickupAddress.state' => ['min:3', 'max:100'],
            'package.pickupAddress.countryId' => ['required', 'integer'],

            'package.arrivalAddress.street' => ['required', 'min:5', 'max:150'],
            'package.arrivalAddress.numExt' => ['required', 'min:1', 'max:50'],
            'package.arrivalAddress.numInt' => ['nullable', 'min:1', 'max:50'],
            'package.arrivalAddress.suburb' => ['min:5', 'max:120'],
            'package.arrivalAddress.postalCode' => ['min:3', 'max:25'],
            'package.arrivalAddress.state' => ['min:3', 'max:100'],
            'package.arrivalAddress.countryId' => ['required', 'integer'],

            'title' => ['required', 'string', 'min:3', 'max: 100'],
            'startDate' => ['required', 'date', 'date_format:Y-m-d', 'after:now'],
            'comment' => ['required', 'string'],
            'addGoogleCalendar' => ['required', 'boolean'],

            'package.nameReceive' => ['required', 'min:3', 'max:150'],
            'package.emailReceive' => ['required', 'min:3', 'max:150'],
            'package.commentReceive' => ['min:3', 'max:2500'],
            'package.officeId' => ['required', 'integer'],
            'package.isUrgent' =>  ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'package.pickupAddress.street' => 'Calle origen',
            'package.pickupAddress.numExt' => 'Número exterior origen',
            'package.pickupAddress.numInt' => 'Número interior origen',
            'package.pickupAddress.suburb' => 'Colonia origen',
            'package.pickupAddress.postalCode' => 'Código postal origen',
            'package.pickupAddress.state' => 'Estado origen',
            'package.pickupAddress.countryId' => 'País origen',

            'package.arrivalAddress.street' => 'Calle destino',
            'package.arrivalAddress.numExt' => 'Número exterior destino',
            'package.arrivalAddress.numInt' => 'Número interior destino',
            'package.arrivalAddress.suburb' => 'Colonia destino',
            'package.arrivalAddress.postalCode' => 'Código postal destino',
            'package.arrivalAddress.state' => 'Estado destino',
            'package.arrivalAddress.countryId' => 'País destino',

            'title' => 'Título',
            'startDate' => 'Fecha de recoger el paquete',
            'comment' => 'Comentarios',
            'addGoogleCalendar' => 'Añadir a Google Calendar',
            'package.isUrgent' => 'Urgente',

            'package.nameReceive' => 'Nombre de quien recibe',
            'package.emailReceive' => 'Correo electrónico de quien recibe',
            'package.officeId' => 'Oficina'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): PackageDTO
    {
        $pickupAddressDTO = new AddressDTO([
            'street' => $this->package['pickupAddress']['street'],
            'num_ext' => $this->package['pickupAddress']['numExt'],
            'num_int' => $this->package['pickupAddress']['numInt'],
            'suburb' => $this->package['pickupAddress']['suburb'],
            'postal_code' => $this->package['pickupAddress']['postalCode'],
            'state' => $this->package['pickupAddress']['state'],
            'country_id' => $this->package['pickupAddress']['countryId']
        ]);

        $arrivalAddressDTO = new AddressDTO([
            'street' => $this->package['arrivalAddress']['street'],
            'num_ext' => $this->package['arrivalAddress']['numExt'],
            'num_int' => $this->package['arrivalAddress']['numInt'],
            'suburb' => $this->package['arrivalAddress']['suburb'],
            'postal_code' => $this->package['arrivalAddress']['postalCode'],
            'state' => $this->package['arrivalAddress']['state'],
            'country_id' => $this->package['arrivalAddress']['countryId']
        ]);

        $requestDTO = new RequestDTO([
            'title' => $this->title,
            'start_date' => $this->startDate,
            'comment' => $this->comment,
            'add_google_calendar' => $this->addGoogleCalendar,
            'user_id' => auth()->id()
        ]);

        return new PackageDTO([
            'name_receive' => $this->package['nameReceive'],
            'email_receive' => $this->package['emailReceive'],
            'request' => $requestDTO,
            'pickupAddress' => $pickupAddressDTO,
            'arrivalAddress' => $arrivalAddressDTO,
            'office_id' => $this->package['officeId'],
            'is_urgent' => $this->package['isUrgent']
        ]);
    }
}
