<?php

namespace App\Http\Requests\RequestDriver;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\CarScheduleDTO;
use App\Models\Dto\DriverRequestScheduleDTO;
use App\Models\Dto\DriverScheduleDTO;
use App\Models\Dto\RequestDriverDTO;
use App\Models\Dto\RequestDTO;
use Illuminate\Foundation\Http\FormRequest;

class ApprovedDriverRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requestId' => ['required', 'integer'],
            'requestDriverId' => ['required', 'integer'],
            'carId' => ['nullable', 'integer'],
            'driverId' => ['nullable', 'integer'],
        ];
    }

    public function attributes(): array
    {
        return [
            'requestId' => 'ID Solicitud',
            'requestDriverId' => 'ID Solicitud de chofer',
            'carId' => 'ID Vehículo',
            'driverId' => 'ID Conductor',
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestDriverDTO
    {
        $requestDTO = new RequestDTO();
        $carScheduleDTO = new CarScheduleDTO(['car_id' => $this->carId]);
        $driverScheduleDTO = new DriverScheduleDTO(['driver_id' => $this->driverId]);
        $driverDriverScheduleDTO = new DriverRequestScheduleDTO([
            'request_driver_id' => $this->requestDriverId,
            'carSchedule' => $carScheduleDTO,
            'driverSchedule' => $driverScheduleDTO
        ]);

        return new RequestDriverDTO([
            'id' => $this->requestDriverId,
            'request_id' => $this->requestId,
            'driverRequestSchedule' => $driverDriverScheduleDTO,
            'request' => $requestDTO
        ]);
    }
}
