<?php

namespace App\Http\Requests\RequestPackage;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\PackageDTO;
use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequestPackageRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'max:3072', 'mimetypes:application/pdf'],
        ];
    }

    public function attributes(): array
    {
        return [
            'file' => 'Archivo de autorización',
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): PackageDTO
    {
        return new PackageDTO([
            'authorization_file' => $this->file
        ]);
    }
}
