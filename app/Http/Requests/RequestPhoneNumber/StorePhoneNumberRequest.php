<?php

namespace App\Http\Requests\RequestPhoneNumber;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RequestPhoneNumberDTO;
use Illuminate\Foundation\Http\FormRequest;

class StorePhoneNumberRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'max:150'],
            'phone' => ['required', 'min:10', 'max:10'],
            'requestId' => ['required', 'integer']
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nombre del contacto',
            'phone' => 'Teléfono de contacto',
            'requestId' => 'Identificador de la solicitud'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestPhoneNumberDTO
    {
        return new RequestPhoneNumberDTO([
            'name' => trim($this->name),
            'phone' => trim($this->phone),
            'request_id' => $this->requestId
        ]);
    }
}
