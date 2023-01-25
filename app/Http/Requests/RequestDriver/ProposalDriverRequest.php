<?php

namespace App\Http\Requests\RequestDriver;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\CarScheduleDTO;
use App\Models\Dto\DriverRequestScheduleDTO;
use App\Models\Dto\DriverScheduleDTO;
use App\Models\Dto\RequestDriverDTO;
use App\Models\Dto\RequestDTO;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ProposalDriverRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requestId' => ['required', 'integer'],
            'driverId' => ['required', 'integer'],
            'carId' => ['required', 'integer'],
            'startDate' => ['required', 'date', 'date_format:Y-m-d H:i:s', 'after:now'],
            'endDate' => ['required', 'date', 'date_format:Y-m-d H:i:s', 'after:startDate']
        ];
    }

    public function attributes(): array
    {
        return [
            'requestId' => 'ID de la solicitud de chofer',
            'driverId' => 'ID del conductor',
            'carId' => 'ID del vehículo',
            'startDate' => 'Fecha de salida',
            'endDate' => 'Fecha de llegada'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestDriverDTO
    {
        $startDate = Carbon::make($this->startDate);
        $endDate = Carbon::make($this->endDate);

        $driverScheduleDTO = new DriverScheduleDTO([
            'driver_id' => $this->driverId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $carScheduleDTO = new CarScheduleDTO([
            'car_id' => $this->carId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $driverRequestScheduleDTO = new DriverRequestScheduleDTO([
            'carSchedule' => $carScheduleDTO,
            'driverSchedule' => $driverScheduleDTO
        ]);

        $requestDTO = new RequestDTO([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        return new RequestDriverDTO([
            'request_id' => $this->requestId,
            'driverRequestSchedule' => $driverRequestScheduleDTO,
            'request' => $requestDTO
        ]);
    }
}
