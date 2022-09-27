<?php

namespace App\Http\Requests\Inventory;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\InventoryDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'name' => ['required', 'min:3', 'max:50'],
            'description' => ['nullable', 'string', 'max:191'],
            'trademark' => ['nullable', 'string', 'max:100'],
            'minimumStock' => ['required', 'integer', 'min:0'],
            'typeId' => ['required', 'integer'],
            'unitId' => ['required', 'integer'],
            'meeting' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'Identificador',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'trademark' => 'Marca',
            'minimumStock' => 'Stock mínimo',
            'typeId' => 'Tipo',
            'unitId' => 'Unidad',
            'status' => 'Estatus',
            'meeting' => 'N° de juntas',
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): InventoryDTO
    {
        return new InventoryDTO([
            'id' =>  $this->id,
            'name' =>  trim($this->name),
            'description' =>  ($this->description) ? trim($this->description) : null,
            'trademark' =>  ($this->trademark) ? trim($this->trademark) : null,
            'minimum_stock' =>  $this->minimumStock,
            'status' =>  $this->status,
            'type_id' =>  $this->typeId,
            'unit_id' =>  $this->unitId,
            'office_id' =>  auth()->user()->office_id,
            'meeting' =>  $this->meeting
        ]);
    }
}
