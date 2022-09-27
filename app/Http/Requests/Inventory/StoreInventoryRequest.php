<?php

namespace App\Http\Requests\Inventory;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\InventoryDTO;
use App\Models\Inventory;
use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'min:3', 'max:50'],
            'description' => ['nullable', 'string', 'max:191'],
            'trademark' => ['nullable', 'string', 'max:100'],
            'stock' => ['required', 'integer', 'min:0', 'max:100'],
            'minimumStock' => ['required', 'integer', 'min:0', 'max:100'],
            'typeId' => ['required', 'integer'],
            'unitId' => ['required', 'integer'],
            'meeting' => ['nullable', 'integer', 'min:1', 'max:25'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nombre',
            'description' => 'Descripción',
            'trademark' => 'Marca',
            'stock' => 'Stock',
            'minimumStock' => 'Stock mínimo',
            'typeId' => 'Tipo',
            'unitId' => 'Unidad',
            'meeting' => 'N° de juntas',
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): InventoryDTO
    {
        return new InventoryDTO([
            'name' =>  trim($this->name),
            'description' =>  ($this->description) ? trim($this->description) : null,
            'trademark' =>  ($this->trademark) ? trim($this->trademark) : null,
            'stock' =>  $this->stock,
            'minimum_stock' =>  $this->minimumStock,
            'status' =>  true,
            'type_id' =>  $this->typeId,
            'unit_id' =>  $this->unitId,
            'office_id' =>  auth()->user()->office_id,
            'meeting' =>  $this->meeting,
            'image' =>  Inventory::IMAGE_DEFAULT
        ]);
    }
}