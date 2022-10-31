<?php

namespace App\Http\Requests\Auth;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\UserDTO;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currentPassword' => ['required', 'min:5', 'max:50'],
            'password' => ['required', 'min:5', 'max:50']
        ];
    }

    public function attributes(): array
    {
        return [
            'currentPassword' => ['Contraseña actual'],
            'password' => ['Contraseña']
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): UserDTO
    {
        return new UserDTO([
            'id' => auth()->id(),
            'currentPassword' => $this->currentPassword,
            'password' => bcrypt($this->password)
        ]);
    }
}