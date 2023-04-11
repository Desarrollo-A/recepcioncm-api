<?php

namespace App\Http\Requests\RequestRoom;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RequestDTO;
use App\Models\Dto\RequestEmailDTO;
use App\Models\Dto\RequestPhoneNumberDTO;
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
            'startDate' => ['required', 'date', 'date_format:Y-m-d H:i:s', 'after:now'],
            'endDate' => ['required', 'date', 'date_format:Y-m-d H:i:s', 'after:startDate'],
            'comment' => ['nullable', 'string'],
            'addGoogleCalendar' => ['required', 'boolean'],
            'people' => ['required', 'integer'],
            'title' => ['required', 'string', 'min:3', 'max: 100'],
            'requestPhoneNumber' => ['array'],
            'requestPhoneNumber.*.name' => ['required', 'max:150'],
            'requestPhoneNumber.*.phone' => ['required', 'min:10', 'max:10'],
            'requestEmail' => ['array'],
            'requestEmail.*.name' => ['required', 'max:150'],
            'requestEmail.*.email' => ['required', 'email:dns', 'max:150'],
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
            'title' => 'Título de la reunión',
            'requestPhoneNumber' => 'Listado de números de teléfono',
            'requestPhoneNumber.*.name' => 'Nombre del contacto',
            'requestPhoneNumber.*.phone' => 'Teléfono de contacto',
            'requestEmail' => 'Listado de correos electrónicos',
            'requestEmail.*.name' => 'Nombre del contacto',
            'requestEmail.*.email' => 'Correo del contacto',
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestRoomDTO
    {
        $now = now();

        $phoneNumbers = array();
        foreach ($this->requestPhoneNumber as $phone) {
            $phoneNumbers[] = new RequestPhoneNumberDTO([
                'name' => $phone['name'],
                'phone' => $phone['phone'],
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        $emails = array();
        foreach ($this->requestEmail as $email) {
            $emails[] = new RequestEmailDTO([
                'name' => $email['name'],
                'email' => $email['email'],
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        $request = new RequestDTO([
            'title' => trim($this->title),
            'start_date' => new Carbon($this->startDate),
            'end_date' => new Carbon($this->endDate),
            'comment' => ($this->comment) ? trim($this->comment) : null,
            'add_google_calendar' => $this->addGoogleCalendar,
            'people' => $this->people,
            'user_id' => auth()->id(),
            'requestPhoneNumber' => $phoneNumbers,
            'requestEmail' => $emails
        ]);

        return new RequestRoomDTO([
            'room_id' => $this->requestRoom['roomId'],
            'external_people' => $this->requestRoom['externalPeople'],
            'level_id' => $this->requestRoom['levelId'],
            'request' => $request,
            'duration' => $request->start_date->diffInMinutes($request->end_date)
        ]);
    }
}
