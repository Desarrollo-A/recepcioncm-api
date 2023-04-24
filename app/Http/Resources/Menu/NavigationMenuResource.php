<?php

namespace App\Http\Resources\Menu;

use App\Http\Resources\Submenu\NavigationSubmenuResource;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationMenuResource extends JsonResource
{
    public function toArray($request): array
    {
        $pathRoute = count($this['submenu']) === 0 ? $this['path_route'] : null;

        return [
            'id' => $this['id'],
            'pathRoute' => $pathRoute,
            'label' => $this['label'],
            'icon' => $this['icon'],
            'order' => $this['order'],
            'isSelected' => $this['is_selected'],
            'submenu' => NavigationSubmenuResource::collection($this['submenu'])
        ];
    }
}
