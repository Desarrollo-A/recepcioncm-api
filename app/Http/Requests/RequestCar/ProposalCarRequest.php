<?php

namespace App\Http\Requests\RequestCar;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\CarRequestScheduleDTO;
use App\Models\Dto\CarScheduleDTO;
use App\Models\Dto\RequestCarDTO;
use App\Models\Dto\RequestDTO;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ProposalCarRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requestId' => ['required', 'integer'],
            'carId' => ['required', 'integer'],
            'startDate' => ['required', 'date', 'date_format:Y-m-d H:i:s', 'after:now'],
            'endDate' => ['required', 'date', 'date_format:Y-m-d H:i:s', 'after:startDate']
        ];
    }

    public function attributes(): array
    {
        return [
            'requestId' => 'ID de la solicitud de chofer',
            'carId' => 'ID del vehículo',
            'startDate' => 'Fecha de salida',
            'endDate' => 'Fecha de llegada'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestCarDTO
    {
        $startDate = Carbon::make($this->startDate);
        $endDate = Carbon::make($this->endDate);

        $carScheduleDTO = new CarScheduleDTO([
            'car_id' => $this->carId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $carRequestScheduleDTO = new CarRequestScheduleDTO([
            'carSchedule' => $carScheduleDTO
        ]);

        $requestDTO = new RequestDTO([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        return new RequestCarDTO([
            'request_id' => $this->requestId,
            'carRequestSchedule' => $carRequestScheduleDTO,
            'request' => $requestDTO
        ]);
    }
}
