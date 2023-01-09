<?php

namespace App\Http\Requests\RequestPackage;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\ProposalRequestDTO;
use Illuminate\Foundation\Http\FormRequest;

class ProposalPackageRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'startDate' => ['required', 'date', 'date_format:Y-m-d', 'after:now'],
            'requestId' => ['required', 'integer']
        ];
    }

    public function attributes(): array
    {
        return [
            'startDate' => 'Fecha de propuesta',
            'requestId' => 'ID de solicitud'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): ProposalRequestDTO
    {
        return new ProposalRequestDTO([
            'request_id' => $this->requestId,
            'start_date' => $this->startDate
        ]);
    }
}
