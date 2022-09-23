<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\UserDTO;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'noEmployee' => ['required', 'min:3', 'max:50'],
            'password' => ['required', 'min:5', 'max:50']
        ];
    }

    public function attributes(): array
    {
        return [
            'noEmployee' => 'No. de colaborador',
            'password' => 'Contraseña'
        ];
    }

    public function toDTO(): UserDTO
    {
        $userDTO = new UserDTO();
        $userDTO->setNoEmployee($this->noEmployee);
        $userDTO->setPassword($this->password);
        return $userDTO;
    }
}
