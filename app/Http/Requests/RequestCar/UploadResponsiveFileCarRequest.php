<?php

namespace App\Http\Requests\RequestCar;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\RequestCarDTO;
use Illuminate\Foundation\Http\FormRequest;

class UploadResponsiveFileCarRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'responsiveFile' => ['required', 'mimes:pdf', 'max:1024']
        ];
    }

    public function attributes(): array
    {
        return [
            'responsiveFile' => 'Archivo de responsiva'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestCarDTO
    {
        return new RequestCarDTO(['responsive_file' => $this->responsiveFile]);
    }
}
