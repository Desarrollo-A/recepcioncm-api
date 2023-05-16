<?php

namespace App\Http\Requests\DeliveredPackage;

use App\Exceptions\CustomErrorException;
use App\Http\Requests\Contracts\ReturnDtoInterface;
use App\Models\Dto\DeliveredPackageDTO;
use Illuminate\Foundation\Http\FormRequest;

class DeliveredPackageRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'packageId' => ['required', 'integer'],
            'nameReceive' => ['required', 'string', 'min:3', 'max:150'],
            'observations' => ['required', 'string', 'min:3', 'max:2500']
        ];
    }

    public function attributes(): array
    {
        return [
            'packageId' => 'ID de paquetería',
            'nameReceive' => 'Nombre de quien recibe',
            'observations' => 'Observaciones'
        ];
    }

    /**
     * @throws CustomErrorException
     */
    public function toDTO(): DeliveredPackageDTO
    {
        return new DeliveredPackageDTO([
            'package_id' => $this->packageId,
            'name_receive' => trim($this->nameReceive),
            'observations' => trim($this->observations)
        ]);
    }
}
