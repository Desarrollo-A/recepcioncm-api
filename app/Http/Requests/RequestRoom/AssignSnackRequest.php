<?php

namespace App\Http\Requests\RequestRoom;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\InventoryRequestDTO;
use App\Models\Dto\RequestRoomDTO;
use Illuminate\Foundation\Http\FormRequest;

class AssignSnackRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requestId' => ['required', 'integer'],
            'inventoryRequest' => ['required', 'array'],
            'inventoryRequest.*.inventoryId' => ['required', 'integer', 'distinct'],
            'inventoryRequest.*.quantity' => ['nullable', 'integer', 'min:0', 'max:100']
        ];
    }

    public function attributes(): array
    {
        return [
            'requestId' => 'Identificador de solicitud',
            'inventoryRequest' => 'Snacks',
            'inventoryRequest.*.inventoryId' => 'Identificador de inventario',
            'inventoryRequest.*.quantity' => 'Cantidad'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): RequestRoomDTO
    {
        $now = now();
        $snacks = array();
        foreach ($this->inventoryRequest as $item) {
            $snacks[] = new InventoryRequestDTO([
                'request_id' =>  $this->requestId,
                'inventory_id' =>  $item['inventoryId'],
                'quantity' =>  $item['quantity'],
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        return new RequestRoomDTO(['request_id' => $this->requestId, 'inventoryRequest' => $snacks]);
    }
}