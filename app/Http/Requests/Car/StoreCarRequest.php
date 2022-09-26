<?php

namespace App\Http\Requests\Car;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\CarDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'businessName' => ['required', 'min:5', 'max:191'],
            'trademark' => ['required', 'min:4', 'max:100'],
            'model' => ['required', 'min:3', 'max:50'],
            'color' => ['required', 'min:3', 'max:50'],
            'licensePlate' => ['required', 'min:3', 'max:10', 'unique:cars,license_plate'],
            'serie' => ['required', 'min:3', 'max:20', 'unique:cars'],
            'circulationCard' => ['required', 'min:3', 'max:10', 'unique:cars,circulation_card'],
            'people' => ['required', 'integer', 'between:1,100']
        ];
    }

    public function attributes(): array
    {
        return [
            'businessName' => 'Razón social',
            'trademark' => 'Marca',
            'model' => 'Modelo',
            'color' => 'Color',
            'licensePlate' => 'Placa',
            'serie' => 'No. de serie',
            'circulationCard' => 'Tarjeta de circulación',
            'people' => 'Capacidad de personas'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): CarDTO
    {
        return new CarDTO([
            'business_name' =>  trim($this->businessName),
            'trademark' =>  trim($this->trademark),
            'model' =>  trim($this->model),
            'color' =>  trim($this->color),
            'license_plate' =>  trim($this->licensePlate),
            'serie' =>  trim($this->serie),
            'circulation_card' =>  trim($this->circulationCard),
            'office_id' =>  auth()->user()->office_id,
            'people' =>  $this->people
        ]);
    }
}