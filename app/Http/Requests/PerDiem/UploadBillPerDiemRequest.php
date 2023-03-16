<?php

namespace App\Http\Requests\PerDiem;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\PerDiemDTO;
use Illuminate\Foundation\Http\FormRequest;

class UploadBillPerDiemRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bill' => ['required', 'mimes:zip', 'max:5120']
        ];
    }

    public function attributes(): array
    {
        return ['bill' => 'Facturas'];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): PerDiemDTO
    {
        return new PerDiemDTO(['bill_file' => $this->bill]);
    }
}
