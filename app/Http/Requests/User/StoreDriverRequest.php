<?php

namespace App\Http\Requests\User;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\OfficeDTO;
use App\Models\Dto\RoleDTO;
use App\Models\Dto\UserDTO;
use App\Models\Enums\NameRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreDriverRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clave' => ['required', 'max:50', 'unique:users,no_employee'],
            'nombreCompleto' => ['required', 'max:150'],
            'correo' => ['required', 'email:dns', 'max:150', 'unique:users,email'],
            'telPersonal' => ['required', 'min:10', 'max:10'],
            'telOficina' => ['nullable', 'min:10', 'max:10'],
            'posicion' => ['required', 'max:100'],
            'area' => ['required', 'max:100'],
            'nombreOficina' => ['required', 'min:3', 'max:150']
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
        $office = new OfficeDTO(['name' => trim($this->nombreOficina)]);
        $role = new RoleDTO(['name' => NameRole::DRIVER]);

        return new UserDTO([
            'no_employee' => trim($this->clave),
            'full_name' => trim($this->nombreCompleto),
            'email' => trim($this->correo),
            'personal_phone' => trim($this->telPersonal),
            'office_phone' => isset($this->telOficina) ? trim($this->telOficina) : $this->telOficina,
            'position' => trim($this->posicion),
            'area' => trim($this->area),
            'role' => $role,
            'office' => $office
        ]);
    }
}
