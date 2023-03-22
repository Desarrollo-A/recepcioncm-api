<?php

namespace App\Http\Requests\RequestPackage;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\CancelRequestDTO;
use App\Models\Dto\LookupDTO;
use App\Models\Dto\RequestDTO;
use App\Models\Enums\Lookups\StatusPackageRequestLookup;
use Illuminate\Foundation\Http\FormRequest;

class AcceptCancelPackageRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $validations = ['status.code' => ['required', 'string', 'bail']];

        if ($this->status['code'] === StatusPackageRequestLookup::code(StatusPackageRequestLookup::CANCELLED)) {
            $validations = array_merge($validations, ['cancelRequest.cancelComment' => ['required', 'string']]);
        }

        return $validations;
    }

    public function attributes(): array
    {
        return [
            'status.code' => 'Clave del estatus',
            'cancelRequest.cancelComment' => 'Comentario de cancelación'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestDTO
    {
        $cancelRequest = new CancelRequestDTO([
            'cancel_comment' => $this->cancelRequest['cancelComment'],
            'user_id' => auth()->id()
        ]);

        $status = new LookupDTO(['code' => $this->status['code']]);

        return new RequestDTO([
            'status' => $status,
            'cancelRequest' => $cancelRequest
        ]);
    }
}
