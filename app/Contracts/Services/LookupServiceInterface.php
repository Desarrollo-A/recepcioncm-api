<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Lookup;
use Illuminate\Database\Eloquent\Collection;

interface LookupServiceInterface extends BaseServiceInterface
{
    public function findAllByType(int $type): Collection;

    /**
     * @return void
     */
    public function validateLookup(int $lookupId, int $type, string $message = 'Lookup no válido');

    public function findByCodeAndType(string $code, int $typeId): Lookup;
}