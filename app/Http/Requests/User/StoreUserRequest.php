<?php

namespace App\Http\Requests\User;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\OfficeDTO;
use App\Models\Dto\RoleDTO;
use App\Models\Dto\UserDTO;
use App\Models\Enums\NameRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'noEmployee' => ['required', 'max:50', 'unique:users,no_employee'],
            'fullName' => ['required', 'max: 150'],
            'email' => ['required', 'email:dns', 'max: 150', 'unique:users'],
            'password' => ['required', 'min:5', 'max: 50'],
            'personalPhone' => ['required', 'min:10', 'max:10'],
            'officePhone' => ['nullable', 'min:10', 'max:10'],
            'position' => ['required', 'max:100'],
            'area' => ['required', 'max:100'],
            'isRecepcionist' => ['required', 'bail', 'boolean'],
            'office.name' => ['required', 'min:3', 'max:150']
        ];
    }

    public function attributes()
    {
        return [
            'noEmployee' => 'Usuario',
            'fullName' => 'Nombre completo',
            'personalPhone' => 'Teléfono personal',
            'officePhone' => 'Teléfono de oficina',
            'position' => 'Puesto',
            'area' => 'Área / Departamento',
            'isRecepcionist' => 'Recepcionista',
            'office.name' => 'Oficina'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): UserDTO
    {
        $office = new OfficeDTO(['name' => trim($this->office['name'])]);
        $role = new RoleDTO(['name' => ($this->isRecepcionist) ? NameRole::RECEPCIONIST : NameRole::APPLICANT]);

        return new UserDTO([
            'no_employee' => trim($this->noEmployee),
            'full_name' => trim($this->fullName),
            'email' => trim($this->email),
            'password' => bcrypt($this->password),
            'personal_phone' => trim($this->personalPhone),
            'office_phone' => isset($this->officePhone) ? trim($this->officePhone) : $this->officePhone,
            'position' => trim($this->position),
            'area' => trim($this->area),
            'role' => $role,
            'office' => $office
        ]);
    }
}
