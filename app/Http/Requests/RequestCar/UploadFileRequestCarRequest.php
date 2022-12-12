<?php

namespace App\Http\Requests\RequestCar;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RequestCarDTO;
use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequestCarRequest extends FormRequest implements ReturnDtoInterface
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
    public function toDTO(): RequestCarDTO
    {
        return new RequestCarDTO([
            'authorization_file' => $this->file
        ]);
    }
}
