<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class GreatherThanZero implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return $value > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'El campo :attribute debe ser mayor a 0';
    }
}
