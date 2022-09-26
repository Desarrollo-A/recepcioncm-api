<?php

namespace App\Http\Requests\Room;

use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RoomDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'name' => ['required', 'min:3', 'max:120'],
            'noPeople' => ['required', 'integer', 'between:1,100']
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'Identificador',
            'name' => 'Nombre',
            'noPeople' => 'No. de Personas'
        ];
    }

    public function toDTO(): RoomDTO
    {
        return new RoomDTO([
            'id' => $this->id,
            'name' => trim($this->name),
            'no_people' => $this->noPeople
        ]);
    }
}