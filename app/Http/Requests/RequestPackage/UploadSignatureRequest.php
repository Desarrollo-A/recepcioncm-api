<?php

namespace App\Http\Requests\RequestPackage;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\PackageDTO;
use Illuminate\Foundation\Http\FormRequest;

class UploadSignatureRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'signature' => ['required', 'mimes:jpg,jpeg,png,gif,svg', 'max:1024']
        ];
    }

    public function attributes(): array
    {
        return [
            'signature' => 'Firma'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): PackageDTO
    {
        return new PackageDTO(['signature_file' => $this->signature]);
    }
}
