<?php

namespace App\Http\Requests\RequestPackage;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\CarScheduleDTO;
use App\Models\Dto\DriverPackageScheduleDTO;
use App\Models\Dto\DriverScheduleDTO;
use App\Models\Dto\PackageDTO;
use App\Models\Dto\ProposalPackageDTO;
use App\Models\Dto\ProposalRequestDTO;
use App\Models\Dto\RequestDTO;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ProposalPackageRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $validations = [
            'startDate' => ['required', 'date', 'date_format:Y-m-d', 'after:now'],
            'requestId' => ['required', 'integer'],
            'isDriverSelected' => ['required', 'boolean', 'bail'],
            'packageId' => ['required', 'integer']
        ];

        if ($this->isDriverSelected) {
            $validations = array_merge($validations, [
                'carId' => ['required', 'integer'],
                'driverId' => ['required', 'integer'],
            ]);
        } else {
            $validations = array_merge($validations, [
                'endDate' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:startDate']
            ]);
        }

        return $validations;
    }

    public function attributes(): array
    {
        return [
            'startDate' => 'Fecha de propuesta',
            'requestId' => 'ID de solicitud',
            'isDriverSelected' => 'Asignación de conductor',
            'packageId' => 'ID de paquetería',
            'carId' => 'ID de vehículo',
            'driverId' => 'ID de conductor',
            'endDate' => 'Fecha de entrega'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): PackageDTO
    {
        $proposalRequest = new ProposalRequestDTO([
            'request_id' => $this->requestId,
            'start_date' => Carbon::make($this->startDate),
            'end_date' => $this->endDate
        ]);

        $proposalPackage = new ProposalPackageDTO([
            'is_driver_selected' => $this->isDriverSelected,
            'package_id' => $this->packageId
        ]);

        $carScheduleDTO = new CarScheduleDTO(['car_id' => $this->carId]);
        $driverScheduleDTO = new DriverScheduleDTO(['driver_id' => $this->driverId]);
        $driverPackageScheduleDTO = new DriverPackageScheduleDTO([
            'package_id' => $this->packageId,
            'carSchedule' => $carScheduleDTO,
            'driverSchedule' => $driverScheduleDTO
        ]);

        return new PackageDTO([
            'id' => $this->packageId,
            'request_id' => $this->requestId,
            'driverPackageSchedule' => $driverPackageScheduleDTO,
            'proposalRequest' => $proposalRequest,
            'proposalPackage' => $proposalPackage,
            'request' => new RequestDTO()
        ]);
    }
}
