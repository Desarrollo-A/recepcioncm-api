<?php

namespace App\Http\Requests\RequestEmail;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RequestEmailDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmailRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'max:150'],
            'email' => ['required', 'email:dns', 'max:150'],
            'requestId' => ['required', 'integer']
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nombre del contacto',
            'email' => 'Correo del contacto',
            'requestId' => 'Identificador de la solicitud'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestEmailDTO
    {
        return new RequestEmailDTO([
            'name' => trim($this->name),
            'email' => trim($this->email),
            'request_id' => $this->requestId
        ]);
    }
}
