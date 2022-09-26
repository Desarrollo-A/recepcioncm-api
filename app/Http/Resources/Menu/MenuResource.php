<?php

namespace App\Http\Resources\Menu;

use App\Http\Resources\Submenu\SubmenuResource;
use Illuminate\Http\Resources\Json\Resource;

class MenuResource extends Resource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $pathRoute = count($this['submenu']) === 0 ? $this['path_route'] : null;

        return [
            'id' => $this['id'],
            'pathRoute' => $pathRoute,
            'label' => $this['label'],
            'icon' => $this['icon'],
            'order' => $this['order'],
            'submenu' => SubmenuResource::collection($this['submenu'])
        ];
    }
}
