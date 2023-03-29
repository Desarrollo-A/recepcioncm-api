<?php

namespace App\Http\Requests\RequestPackage;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\CarScheduleDTO;
use App\Models\Dto\DetailExternalParcelDTO;
use App\Models\Dto\DriverPackageScheduleDTO;
use App\Models\Dto\DriverScheduleDTO;
use App\Models\Dto\PackageDTO;
use App\Models\Dto\RequestDTO;
use Illuminate\Foundation\Http\FormRequest;

class ApprovedPackageRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requestId' => ['required', 'integer'],
            'packageId' => ['nullable', 'integer'],
            'carId' => ['nullable', 'integer'],
            'driverId' => ['nullable', 'integer'],
            'endDate' => ['nullable', 'date', 'date_format:Y-m-d', 'after:now'],
            'companyName' => ['nullable', 'min:3', 'max:75'],
            'trackingCode' => ['nullable', 'min:10', 'max:25'],
            'urlTracking' => ['nullable', 'min:10', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'requestId' => 'ID Solicitud',
            'carId' => 'ID Vehículo',
            'driverId' => 'ID Conductor',
            'packageId' => 'ID Paquetería',
            'endDate' => 'Fecha de llegada',
            'companyName' => 'Nombre de la paquetería',
            'trackingCode' => 'Código de rastreo',
            'urlTracking' => 'URL para consultar el código',
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): PackageDTO
    {
        $requestDTO = new RequestDTO();

        if (isset($this->trackingCode)) {
            $requestDTO->end_date = $this->endDate;
        }

        if (!isset($this->trackingCode)) {
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
                'request' => $requestDTO,
                'driverPackageSchedule' => $driverPackageScheduleDTO
            ]);
        }

        $detailExternalParcel = new DetailExternalParcelDTO([
            'package_id' => $this->packageId,
            'company_name' => $this->companyName,
            'tracking_code' => $this->trackingCode,
            'url_tracking' => $this->urlTracking
        ]);

        return new PackageDTO([
            'id' => $this->packageId,
            'request_id' => $this->requestId,
            'request' => $requestDTO,
            'detailExternalParcel' => $detailExternalParcel
        ]);
    }
}
