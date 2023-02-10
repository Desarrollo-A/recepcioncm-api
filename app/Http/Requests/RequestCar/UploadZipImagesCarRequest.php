<?php

namespace App\Http\Requests\RequestCar;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RequestCarDTO;
use Illuminate\Foundation\Http\FormRequest;

class UploadZipImagesCarRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'imageZipFile' => ['required', 'mimes:zip', 'max:5120']
        ];
    }

    public function attributes(): array
    {
        return [
            'imageZipFile' => 'Archivo ZIP'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestCarDTO
    {
        return new RequestCarDTO(['image_zip_file' => $this->imageZipFile]);
    }
}
