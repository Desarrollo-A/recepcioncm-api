<?php

namespace App\Http\Requests\RequestCar;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RequestCarDTO;
use App\Models\Dto\RequestDTO;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequestCarRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max: 100'],
            'startDate' => ['required', 'date', 'date_format:Y-m-d H:i', 'after:now'],
            'endDate' => ['required', 'date', 'date_format:Y-m-d H:i', 'after:startDate'],
            'people' => ['required', 'integer', 'min:2'],
            'comment' => ['nullable', 'string'],
            'addGoogleCalendar' => ['required', 'boolean'],

            'requestCar.officeId' => ['required', 'integer']
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Título',
            'startDate' => 'Fecha de salida',
            'endDate' => 'Fecha de llegada',
            'people' => 'N° de personas',
            'comment' => 'Comentarios',
            'addGoogleCalendar' => 'Añadir a Google Calendar',

            'requestCar.officeId' => 'Oficina'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestCarDTO
    {
        $requestDTO = new RequestDTO([
            'title' => $this->title,
            'start_date' => new Carbon($this->startDate),
            'end_date' => new Carbon($this->endDate),
            'people' => $this->people,
            'comment' => $this->comment,
            'add_google_calendar' => $this->addGoogleCalendar,
            'user_id' => auth()->id()
        ]);

        return new RequestCarDTO([
            'request' => $requestDTO,
            'office_id' => $this->requestCar['officeId']
        ]);
    }
}
