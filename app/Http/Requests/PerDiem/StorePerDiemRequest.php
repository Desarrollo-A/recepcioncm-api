<?php

namespace App\Http\Requests\PerDiem;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\PerDiemDTO;
use Illuminate\Foundation\Http\FormRequest;

class StorePerDiemRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requestId' => ['required', 'integer'],
            'gasoline' => ['required', 'numeric', 'gt:0'],
            'tollbooths' => ['required', 'numeric', 'gte:0'],
            'food' => ['required', 'numeric', 'gte:0']
        ];
    }

    public function attributes(): array
    {
        return [
            'requestId' => 'ID de solicitud',
            'gasoline' => 'Gasolina',
            'tollbooths' => 'Casetas de cobro',
            'food' => 'Alimentos'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): PerDiemDTO
    {
        return new PerDiemDTO([
            'request_id' => $this->requestId,
            'gasoline' => $this->gasoline,
            'tollbooths' => $this->tollbooths,
            'food' => $this->food
        ]);
    }
}
