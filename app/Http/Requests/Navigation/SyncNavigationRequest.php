<?php

namespace App\Http\Requests\Navigation;

use App\Http\Requests\Contracts\ReturnDtoInterface;
use Illuminate\Foundation\Http\FormRequest;

class SyncNavigationRequest extends FormRequest implements ReturnDtoInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'menus' => ['required', 'array'],
            'menus.*' => ['numeric'],
            'submenus' => ['required', 'array'],
            'submenus.*' => ['numeric']
        ];
    }

    public function attributes(): array
    {
        return [
            'menus' => 'Menús',
            'menus.*' => 'Menú :position',
            'submenus' => 'Submenús',
            'submenus.*' => 'Submenú :position'
        ];
    }

    /**
     * @return array ['menus' => array, 'submenus' => array]
     */
    public function toDTO(): array
    {
        return [
            'menus' => $this->menus,
            'submenus' => $this->submenus
        ];
    }
}
