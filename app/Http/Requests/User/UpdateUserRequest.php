<?php

namespace App\Http\Requests\User;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\UserDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'bail'],
            'noEmployee' => [
                'required',
                'max:50',
                Rule::unique('users', 'no_employee')->ignore($this->id, 'id')
            ],
            'fullName' => ['required', 'max:150'],
            'email' => [
                'required',
                'email:dns',
                'max:150',
                Rule::unique('users', 'email')->ignore($this->id, 'id')
            ],
            'personalPhone' => ['required', 'min:10', 'max:10'],
            'officePhone' => ['nullable', 'min:10', 'max:10'],
            'position' => ['required', 'max:100'],
            'area' => ['required', 'max:100'],
            'officeId' => ['required', 'integer'],
            'statusId' => ['required', 'integer']
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'Identificador',
            'noEmployee' => 'Usuario',
            'fullName' => 'Nombre completo',
            'email' => 'Correo electrónico',
            'personalPhone' => 'Teléfono personal',
            'officePhone' => 'Teléfono de oficina',
            'position' => 'Puesto',
            'area' => 'Área / Departamento',
            'officeId' => 'Oficina',
            'statusId' => 'Estatus'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): UserDTO
    {
        return new UserDTO([
            'no_employee' => trim($this->noEmployee),
            'full_name' => trim($this->fullName),
            'email' => trim($this->email),
            'personal_phone' => trim($this->personalPhone),
            'office_phone' => isset($this->officePhone) ? trim($this->officePhone) : $this->officePhone,
            'position' => trim($this->position),
            'area' => trim($this->area),
            'office_id' => $this->officeId,
            'status_id' => $this->statusId
        ]);
    }
}
