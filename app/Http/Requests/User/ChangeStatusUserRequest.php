<?php

namespace App\Http\Requests\User;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\UserDTO;
use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusUserRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'statusId' => ['required', 'integer']
        ];
    }

    public function attributes(): array
    {
        return ['statusId' => 'Estatus'];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): UserDTO
    {
        return new UserDTO(['status_id' => $this->statusId]);
    }
}
