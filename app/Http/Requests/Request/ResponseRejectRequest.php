<?php

namespace App\Http\Requests\Request;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\LookupDTO;
use App\Models\Dto\RequestDTO;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ResponseRejectRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status.code' => ['required', 'string'],
            'proposalId' => ['nullable', 'integer']
        ];
    }

    public function attributes(): array
    {
        return [
            'status.code' => 'Clave del estatus',
            'proposalId' => 'ID de la propuesta'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestDTO
    {
        $status = new LookupDTO(['code' => $this->status['code']]);

        return new RequestDTO([
            'status' => $status,
            'proposal_id' => $this->proposalId
        ]);
    }
}
