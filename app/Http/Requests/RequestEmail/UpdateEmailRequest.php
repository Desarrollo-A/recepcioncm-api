<?php

namespace App\Http\Requests\RequestEmail;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RequestEmailDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'name' => ['required', 'max:150'],
            'email' => ['required', 'email:dns', 'max:150'],
            'requestId' => ['required', 'integer']
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'Identificador',
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
            'id' => $this->id,
            'name' => trim($this->name),
            'email' => trim($this->email),
            'request_id' => $this->requestId
        ]);
    }
}
