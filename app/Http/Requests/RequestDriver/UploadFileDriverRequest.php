<?php

namespace App\Http\Requests\RequestDriver;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RequestDriverDTO;
use Illuminate\Foundation\Http\FormRequest;

class UploadFileDriverRequest extends FormRequest implements ReturnDtoInterface
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
    public function toDTO(): RequestDriverDTO
    {
        return new RequestDriverDTO(['authorization_file' => $this->file]);
    }
}
