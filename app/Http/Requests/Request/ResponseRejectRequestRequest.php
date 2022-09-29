<?php

namespace App\Http\Requests\Request;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\LookupDTO;
use App\Models\Dto\RequestDTO;
use Carbon\Carbon;
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
            'startDate' => ['date', 'date_format:Y-m-d H:i:s', 'after:now'],
            'endDate' => ['date', 'date_format:Y-m-d H:i:s', 'after:startDate'],
        ];
    }

    public function attributes(): array
    {
        return [
            'status.code' => 'Clave del estatus',
            'startDate' => 'Fecha inicio',
            'endDate' => 'Fecha fin'
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
            'start_date' => (!is_null($this->startDate)) ? new Carbon($this->startDate) : null,
            'end_date' => (!is_null($this->endDate)) ? new Carbon($this->endDate) : null
        ]);
    }
}
