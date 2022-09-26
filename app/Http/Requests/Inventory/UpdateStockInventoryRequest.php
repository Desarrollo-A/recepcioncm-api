<?php

namespace App\Http\Requests\Inventory;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\InventoryDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStockInventoryRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $validations = ['stock' => ['required', 'integer', 'min:-100', 'max:100', 'not_in:0', 'bail']];

        if ($this->stock > 0) {
            $validations = array_merge($validations, [ 'cost' => [ 'required', 'numeric', 'bail', 'gt:0', 'lt:100000' ] ]);
        }

        return $validations;
    }

    public function attributes(): array
    {
        return [
            'stock' => 'Stock',
            'cost' => 'Costo'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): InventoryDTO
    {
        return new InventoryDTO([
            'stock' => $this->stock,
            'cost' => ($this->stock > 0) ? $this->cost : null
        ]);
    }
}