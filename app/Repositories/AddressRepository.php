<?php

namespace App\Repositories;

use App\Contracts\Repositories\AddressRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Address;

class AddressRepository extends BaseRepository implements AddressRepositoryInterface
{
    protected $entity;

    public function __construct(Address $address)
    {
        $this->entity = $address;
    }
}