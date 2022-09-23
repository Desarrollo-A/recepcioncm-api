<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\Office\OfficeResource;
use App\Http\Resources\Role\RoleResource;
use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
    private $token;

    public function __construct($resource, string $token = '')
    {
        parent::__construct($resource);
        $this->token = $token;
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'noEmployee' => $this->no_employee,
            'fullName' => $this->full_name,
            'email' => $this->email,
            'personalPhone' => $this->personal_phone,
            'officePhone' => $this->office_phone,
            'position' => $this->position,
            'area' => $this->area,
            'roleId' => $this->role_id,
            'statusId' => $this->status_id,
            'officeId' => $this->office_id,
            'role' => RoleResource::make($this->whenLoaded('role')),
            'status' => LookupResource::make($this->whenLoaded('status')),
            'office' => OfficeResource::make($this->whenLoaded('office')),
            'token' => $this->when( !empty($this->token), $this->token)
        ];
    }
}
