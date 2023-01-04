<?php

namespace App\Http\Requests\RequestCar;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\CarRequestScheduleDTO;
use App\Models\Dto\CarScheduleDTO;
use App\Models\Dto\RequestCarDTO;
use App\Models\Dto\RequestDTO;
use Illuminate\Foundation\Http\FormRequest;

class ApprovedCarRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requestId' => ['required', 'integer'],
            'requestCarId' => ['required', 'integer'],
            'carId' => ['nullable', 'integer']
        ];
    }

    public function attributes(): array
    {
        return [
            'requestId' => 'ID Solicitud',
            'requestCarId' => 'ID Solicitud de vehiculo',
            'carId' => 'ID Vehículo',
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestCarDTO
    {
        $requestDTO = new RequestDTO();
        $carScheduleDTO = new CarScheduleDTO(['car_id' => $this->carId]);
        $carRequestScheduleDTO = new CarRequestScheduleDTO([
            'request_car_id' => $this->requestCarId,
            'carSchedule' => $carScheduleDTO
        ]);

        return new RequestCarDTO([
            'id' => $this->requestCarId,
            'request_id' => $this->requestId,
            'carRequestSchedule' => $carRequestScheduleDTO,
            'request' => $requestDTO
        ]);
    }
}
