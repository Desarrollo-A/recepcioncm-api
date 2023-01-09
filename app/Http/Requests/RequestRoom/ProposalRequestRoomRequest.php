<?php

namespace App\Http\Requests\RequestRoom;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\ProposalRequestDTO;
use App\Models\Dto\RequestDTO;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ProposalRequestRoomRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'startDate1' => ['required', 'date', 'date_format:Y-m-d H:i', 'after:now'],
            'endDate1' => ['required', 'date', 'date_format:Y-m-d H:i', 'after:startDate1'],
            'startDate2' => ['required', 'date', 'date_format:Y-m-d H:i', 'after:now'],
            'endDate2' => ['required', 'date', 'date_format:Y-m-d H:i', 'after:startDate2']
        ];
    }

    public function attributes(): array
    {
        return [
            'startDate1' => 'Fecha inicio 1',
            'endDate1' => 'Fecha fin 1',
            'startDate2' => 'Fecha inicio 2',
            'endDate2' => 'Fecha fin 2'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestDTO
    {
        $proposalRequest = [];
        $proposalRequest[] = new ProposalRequestDTO([
            'start_date' => new Carbon($this->startDate1),
            'end_date' => new Carbon($this->endDate1)
        ]);
        $proposalRequest[] = new ProposalRequestDTO([
            'start_date' => new Carbon($this->startDate2),
            'end_date' => new Carbon($this->endDate2)
        ]);

        return new RequestDTO(['proposalRequest' => $proposalRequest]);
    }
}
