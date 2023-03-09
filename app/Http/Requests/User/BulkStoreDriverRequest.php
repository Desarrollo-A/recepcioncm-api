<?php

namespace App\Http\Requests\User;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\BulkLoadFileDTO;
use Illuminate\Foundation\Http\FormRequest;

class BulkStoreDriverRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'mimes:xlsx,xls', 'max:10240']
        ];
    }

    public function attributes(): array
    {
        return [
            'file' => 'Archivo'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): BulkLoadFileDTO
    {
        return new BulkLoadFileDTO(['file' => $this->file]);
    }
}
