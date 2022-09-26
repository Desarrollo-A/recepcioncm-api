<?php

namespace App\Http\Requests\RequestRoom;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\CancelRequestDTO;
use Illuminate\Foundation\Http\FormRequest;

class CancelRequestRoomRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cancelComment' => ['required', 'string']
        ];
    }

    public function attributes(): array
    {
        return [
            'cancelComment' => 'Comentario'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): CancelRequestDTO
    {
        return new CancelRequestDTO([
            'cancel_comment' => $this->cancelComment,
            'user_id' => auth()->id()
        ]);
    }
}
