<?php

namespace App\Http\Requests\Room;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RoomDTO;
use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusRoomRequest extends FormRequest implements ReturnDtoInterface
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
    public function toDTO(): RoomDTO
    {
        return new RoomDTO(['status_id' => $this->statusId]);
    }
}
