<?php

namespace App\Http\Requests\DeliveredPackage;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\DeliveredPackageDTO;
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
            'signatureFile' => ['required', 'mimes:jpg,jpeg,png,gif,svg', 'max:1024']
        ];
    }

    public function attributes(): array
    {
        return [
            'signatureFile' => 'Firma'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): DeliveredPackageDTO
    {
        return new DeliveredPackageDTO(['signature_file' => $this->signatureFile]);
    }
}
