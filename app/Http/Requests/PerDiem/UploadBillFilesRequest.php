<?php

namespace App\Http\Requests\PerDiem;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\FileDTO;
use Illuminate\Foundation\Http\FormRequest;

class UploadBillFilesRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'files' => ['array'],
            'files.*' => ['required', 'mimes:pdf,xml', 'max:3000']
        ];
    }

    public function attributes(): array
    {
        return [
            'files' => 'Archivos'
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
