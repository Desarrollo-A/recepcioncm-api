<?php

namespace App\Http\Requests\RequestDriver;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RequestDriverDTO;
use Illuminate\Foundation\Http\FormRequest;

class TransferDriverRequest extends FormRequest implements ReturnDtoInterface
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

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestDriverDTO
    {
        return new RequestDriverDTO(['office_id' => $this->officeId]);
    }
}
