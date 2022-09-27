<?php

namespace App\Http\Requests\Auth;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\UserDTO;
use Illuminate\Foundation\Http\FormRequest;

class RestorePasswordRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email:dns', 'max:150']
        ];
    }

    public function attributes(): array
    {
        return ['email' => 'Correo electrónico'];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): UserDTO
    {
        return new UserDTO(['email' => $this->email]);
    }
}
