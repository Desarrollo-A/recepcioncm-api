<?php

namespace App\Http\Resources\Room;

use App\Http\Resources\Traits\PaginateCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RoomCollection extends ResourceCollection
{
    use PaginateCollection;

    private $paginated;
    public $collects = RoomResource::class;

    public function __construct($resource, bool $paginated = false)
    {
        parent::__construct($resource);
        $this->paginated = $paginated;
    }

    public function toArray($request): array
    {
        return ($this->paginated)
            ? $this->getPaginationCollection($this)
            : parent::toArray($request);
    }
}
