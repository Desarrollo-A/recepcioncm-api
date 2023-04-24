<?php

namespace App\Http\Requests\RequestCar;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\FileDTO;
use Illuminate\Foundation\Http\FormRequest;

class UploadImagesFilesRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'files' => ['array'],
            'files.*' => ['required', 'mimes:jpeg,jpg,jpe,png,gif', 'max:5000']
        ];
    }

    public function attributes(): array
    {
        return [
            'files' => 'Archivos',
            'files.*' => 'Archivo :position'
        ];
    }

    /**
     * @return FileDTO[]
     * @throws CustomErrorException
     */
    public function toDTO(): array
    {
        $data = [];

        foreach ($this->files as $files) {
            foreach ($files as $file) {
                $data[] = new FileDTO(['file' => $file]);
            }
        }

        return $data;
    }
}
