<?php

namespace App\Http\Requests\User;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\OfficeDTO;
use App\Models\Dto\UserDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserChRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clave' => [
                'nullable',
                'max:50',
                Rule::unique('users', 'no_employee')->ignore($this->route('noEmployee'), 'no_employee')
            ],
            'nombreCompleto' => ['nullable', 'max:150'],
            'correo' => [
                'nullable',
                'email:dns',
                'max:150',
                Rule::unique('users', 'email')->ignore($this->route('noEmployee'), 'email')
            ],
            'telPersonal' => ['nullable', 'min:10', 'max:10'],
            'telOficina' => ['nullable', 'min:10', 'max:10'],
            'position' => ['nullable', 'max:100'],
            'area' => ['nullable', 'max:100'],
            'nombreOficina' => ['nullable', 'min:3', 'max:150']
        ];
    }

    public function attributes(): array
    {
        return [
            'clave' => '# de colaborador',
            'nombreCompleto' => 'Nombre completo',
            'correo' => 'Correo electrónico',
            'telPersonal' => 'Teléfono personal',
            'telOficina' => 'Teléfono oficina',
            'posicion' => 'Posición',
            'area' => 'Área',
            'nombreOficina' => 'Nombre de oficina',
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): UserDTO
    {
        $office = new OfficeDTO([
            'name' => isset($this->nombreOficina) ? trim($this->nombreOficina) : null
        ]);

        return new UserDTO([
            'no_employee' => isset($this->clave) ? trim($this->clave) : null,
            'full_name' => isset($this->nombreCompleto) ? trim($this->nombreCompleto) : null,
            'email' => isset($this->correo) ? trim($this->correo) : null,
            'personal_phone' => isset($this->telPersonal) ? trim($this->telPersonal) : null,
            'office_phone' => isset($this->telOficina) ? trim($this->telOficina) : null,
            'position' => isset($this->posicion) ? trim($this->posicion) : null,
            'area' => isset($this->area) ? trim($this->area) : null,
            'office' => $office
        ]);
    }
}
