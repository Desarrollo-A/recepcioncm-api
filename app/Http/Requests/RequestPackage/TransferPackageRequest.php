<?php

namespace App\Http\Requests\RequestPackage;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\PackageDTO;
use Illuminate\Foundation\Http\FormRequest;

class TransferPackageRequest extends FormRequest implements ReturnDtoInterface
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
    public function toDTO(): PackageDTO
    {
        return new PackageDTO(['office_id' => $this->officeId]);
    }
}
