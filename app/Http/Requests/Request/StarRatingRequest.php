<?php

namespace App\Http\Requests\Request;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\ScoreDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StarRatingRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requestId' => ['required', 'integer'],
            'score' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5])],
            'comment' => ['nullable', 'string', 'max:2500']
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): ScoreDTO
    {
        return new ScoreDTO([
            'request_id' => $this->requestId,
            'score' => $this->score,
            'comment' => $this->comment
        ]);
    }
}
