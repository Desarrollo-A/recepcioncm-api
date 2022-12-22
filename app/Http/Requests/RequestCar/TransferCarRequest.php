<?php

namespace App\Http\Requests\RequestCar;

use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RequestCarDTO;
use Illuminate\Foundation\Http\FormRequest;

class TransferCarRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'officeId' => ['required', 'integer']
        ];
    }

    public function attributes(): array
    {
        return [
            'officeId' => 'Oficina'
        ];
    }

    public function toDTO(): RequestCarDTO
    {
        return new RequestCarDTO(['office_id' => $this->officeId]);
    }
}
