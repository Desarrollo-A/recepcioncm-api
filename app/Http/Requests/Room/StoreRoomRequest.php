<?php

namespace App\Http\Requests\Room;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RoomDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'min:3', 'max:120'],
            'noPeople' => ['required', 'integer', 'between:1,100']
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nombre',
            'noPeople' => 'No. de Personas'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RoomDTO
    {
        $user = auth()->user();

        return new RoomDTO([
            'name' => trim($this->name),
            'no_people' => $this->noPeople,
            'office_id' => $user->office_id,
            'recepcionist_id' => $user->id
        ]);
    }
}
