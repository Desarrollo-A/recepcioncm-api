<?php

namespace App\Http\Requests\Office;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\AddressDTO;
use App\Models\Dto\OfficeDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreOfficeRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'min:3', 'max:120'],
            'stateId' => ['required', 'integer'],
            'address.street' => ['required', 'min:5', 'max:150'],
            'address.numExt' => ['required', 'max:50'],
            'address.numInt' => ['nullable', 'min:1', 'max:50'],
            'address.suburb' => ['required', 'min:5', 'max:120'],
            'address.postalCode' => ['required', 'min:3', 'max:25'],
            'address.state' => ['required', 'min:3', 'max:100'],
            'address.countryId' => ['required', 'integer']
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nombre',
            'stateId' => 'Sede',
            'address.street' => 'Calle',
            'address.numExt' => 'Número exterior',
            'address.numInt' => 'Número interior',
            'address.suburb' => 'Colonia',
            'address.postalCode' => 'Código postal',
            'address.state' => 'Estado',
            'address.countryId' => 'País'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): OfficeDTO
    {
        $address = new AddressDTO([
            'street' => trim($this->address['street']),
            'num_ext' => trim($this->address['numExt']),
            'num_int' => trim($this->address['numInt']),
            'suburb' => trim($this->address['suburb']),
            'postal_code' => trim($this->address['postalCode']),
            'state' => trim($this->address['state']),
            'country_id' => $this->address['countryId']
        ]);

        return new OfficeDTO([
            'name' => trim($this->name),
            'state_id' => $this->stateId,
            'address' => $address
        ]);
    }
}
