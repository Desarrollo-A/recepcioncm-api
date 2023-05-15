<?php

namespace App\Http\Requests\Driver;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\DriverParcelDayDTO;
use App\Models\Dto\UserDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateParcelDaysRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'driverParcelDay' => ['required', 'array'],
            'driverParcelDay.*.dayId' => ['required', 'integer']
        ];
    }

    public function attributes(): array
    {
        return [
            'driverParcelDay' => 'Días de paquetería',
            'driverParcelDay.*.dayId' => 'Día, posición :position'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): UserDTO
    {
        $data = [];

        foreach($this->driverParcelDay as $item) {
            $data[] = new DriverParcelDayDTO(['day_id' => $item['dayId']]);
        }

        return new UserDTO(['driverParcelDays' => $data]);
    }
}
