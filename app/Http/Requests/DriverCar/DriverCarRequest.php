<?php

namespace App\Http\Requests\DriverCar;

use App\Models\Dto\DriverCarDTO;
use Illuminate\Foundation\Http\FormRequest;

class DriverCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return[
            'carId'     =>  ['required', 'integer'],
            'driverId'  =>  ['required', 'integer']
        ];
    }

    public function attributes(): array
    {
        return [
            'carId'     =>  'Id de vehiculo',
            'driverId'  =>  'Id del conductor'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return CustomErrorException
     */
    public function toDto(): DriverCarDTO
    {
        return new DriverCarDTO([
            'car_id'        =>  $this->carId,
            'driver_id'     =>  $this->driverId
        ]);
    }
}
