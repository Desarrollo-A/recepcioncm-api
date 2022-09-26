<?php

namespace App\Http\Requests\InventoryRequest;

use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\InventoryRequestDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequestRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requestId' => ['required', 'integer'],
            'inventoryId' => ['required', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:100']
        ];
    }

    public function attributes(): array
    {
        return [
            'requestId' => 'Solicitud',
            'inventoryId' => 'Snack',
            'quantity' => 'cantidad'
        ];
    }

    public function toDTO(): InventoryRequestDTO
    {
        return new InventoryRequestDTO([
            'request_id' =>  $this->requestId,
            'inventory_id' =>  $this->inventoryId,
            'quantity' =>  $this->quantity,
            'applied' =>  !is_null($this->quantity)
        ]);
    }
}
