<?php

namespace App\Http\Requests\RequestRoom;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RequestDTO;
use App\Models\Dto\RequestRoomDTO;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequestRoomRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requestRoom.roomId' => ['required', 'integer'],
            'requestRoom.externalPeople' => ['required', 'integer'],
            'requestRoom.levelId' => ['required', 'integer'],
            'startDate' => ['required', 'date', 'date_format:Y-m-d H:i', 'after:now'],
            'endDate' => ['required', 'date', 'date_format:Y-m-d H:i', 'after:startDate'],
            'comment' => ['nullable', 'string'],
            'addGoogleCalendar' => ['required', 'boolean'],
            'people' => ['required', 'integer'],
            'title' => ['required', 'string', 'min:3', 'max: 100']
        ];
    }

    public function attributes(): array
    {
        return [
            'requestRoom.roomId' => 'Sala',
            'requestRoom.externalPeople' => 'No. de personas externas',
            'requestRoom.levelId' => 'Tipo de junta',
            'startDate' => 'Fecha inicio',
            'endDate' => 'Fecha fin',
            'comment' => 'Comentario',
            'addGoogleCalendar' => 'Agregar al Google Calendar',
            'people' => 'No. de personas',
            'title' => 'Título de la reunión'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestRoomDTO
    {
        $request = new RequestDTO([
            'title' => trim($this->title),
            'start_date' => new Carbon($this->startDate),
            'end_date' => new Carbon($this->endDate),
            'comment' => ($this->comment) ? trim($this->comment) : null,
            'add_google_calendar' => $this->addGoogleCalendar,
            'people' => $this->people,
            'user_id' => auth()->id()
        ]);

        $request->duration = $request->start_date->diffInMinutes($request->end_date);

        return new RequestRoomDTO([
            'room_id' => $this->requestRoom['roomId'],
            'external_people' => $this->requestRoom['externalPeople'],
            'level_id' => $this->requestRoom['levelId'],
            'request' => $request
        ]);
    }
}
