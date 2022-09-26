<?php

namespace App\Http\Requests\Request;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\LookupDTO;
use App\Models\Dto\RequestDTO;
use Illuminate\Foundation\Http\FormRequest;

class ResponseRejectRequestRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status.code' => ['required', 'string'],
            'statusId' => ['required', 'integer']
        ];
    }

    public function attributes(): array
    {
        return [
            'status.code' => 'Clave del estatus',
            'statusId' => 'Estatus'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestDTO
    {
        $status = new LookupDTO(['code' => $this->status['code']]);
        return new RequestDTO(['status_id' => $this->statusId, 'status' => $status]);
    }
}
