<?php

namespace App\Http\Requests\PerDiem;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\PerDiemDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSpentPerDiemRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'spent' => ['required', 'numeric', 'gt:0']
        ];
    }

    public function attributes(): array
    {
        return ['spent' => 'Total gastado'];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): PerDiemDTO
    {
        return new PerDiemDTO(['spent' => $this->spent]);
    }
}
