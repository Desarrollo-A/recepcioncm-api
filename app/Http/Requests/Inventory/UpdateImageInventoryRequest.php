<?php

namespace App\Http\Requests\Inventory;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\InventoryDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateImageInventoryRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'max:2048']
        ];
    }

    public function attributes(): array
    {
        return [
            'image' => 'Imagen'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): InventoryDTO
    {
        return new InventoryDTO(['image_file' => $this->image]);
    }
}
