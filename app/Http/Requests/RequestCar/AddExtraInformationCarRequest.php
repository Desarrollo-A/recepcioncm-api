<?php

namespace App\Http\Requests\RequestCar;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RequestCarDTO;
use Illuminate\Foundation\Http\FormRequest;

class AddExtraInformationCarRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'initialKm' => ['nullable', 'integer', 'min:1', 'max:999999'],
            'finalKm' => ['nullable', 'integer', 'min:1', 'max:999999'],
            'deliveryCondition' => ['nullable', 'string', 'max:2500']
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestCarDTO
    {
        return new RequestCarDTO([
            'initial_km' => $this->initialKm,
            'final_km' => $this->finalKm,
            'delivery_condition' => $this->deliveryCondition
        ]);
    }
}
