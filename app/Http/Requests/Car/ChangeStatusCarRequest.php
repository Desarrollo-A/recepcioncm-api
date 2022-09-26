<?php

namespace App\Http\Requests\Car;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\CarDTO;
use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusCarRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'statusId' => ['required', 'integer']
        ];
    }

    public function attributes()
    {
        return ['statusId' => 'Estatus'];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): CarDTO
    {
        return new CarDTO(['status_id' => $this->statusId]);
    }
}
