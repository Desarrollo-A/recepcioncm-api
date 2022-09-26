<?php

namespace App\Http\Requests\Car;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\CarDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCarRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'businessName' => ['required', 'min:5', 'max:191'],
            'trademark' => ['required', 'min:4', 'max:100'],
            'model' => ['required', 'min:3', 'max:50'],
            'color' => ['required', 'min:3', 'max:50'],
            'licensePlate' => ['required', 'min:3', 'max:10',
                Rule::unique('cars', 'license_plate')->ignore($this->id, 'id')],
            'serie' => ['required', 'min:3', 'max:20',
                Rule::unique('cars', 'serie')->ignore($this->id, 'id')],
            'circulationCard' => ['required', 'min:3', 'max:10',
                Rule::unique('cars', 'circulation_card')->ignore($this->id, 'id')],
            'people' => ['required', 'integer', 'between:1,100']
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'Identificador',
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
            'id' =>  $this->id,
            'businessName' =>  trim($this->businessName),
            'trademark' =>  trim($this->trademark),
            'model' =>  trim($this->model),
            'color' =>  trim($this->color),
            'licensePlate' =>  trim($this->licensePlate),
            'serie' =>  trim($this->serie),
            'circulationCard' =>  trim($this->circulationCard),
            'officeId' =>  auth()->user()->office_id,
            'people' =>  $this->people
        ]);
    }
}