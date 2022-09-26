<?php

namespace App\Http\Requests\RequestRoom;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RequestDTO;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AvailableScheduleRequestRoomRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'startDate' => ['required', 'date', 'date_format:Y-m-d H:i:s'],
            'endDate' => ['required', 'date', 'date_format:Y-m-d H:i:s', 'after:startDate'],
        ];
    }

    public function attributes(): array
    {
        return [
            'startDate' => 'Fecha inicio',
            'endDate' => 'Fecha fin',
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestDTO
    {
        return new RequestDTO([
            'start_date' => new Carbon($this->startDate),
            'end_date' => new Carbon($this->endDate)
        ]);
    }
}