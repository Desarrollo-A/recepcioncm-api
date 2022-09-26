<?php

namespace App\Http\Resources\Traits;

use Illuminate\Http\Resources\Json\ResourceCollection;

trait PaginateCollection
{
    /**
     * Función para retornar una respuesta de ResourceCollection con paginación
     * @param ResourceCollection $collection
     * @return array
     */
    public function getPaginationCollection(ResourceCollection $collection): array
    {
        return [
            'data' => $collection->collection,
            'links' => [
                'first' => ($collection->onFirstPage()) ? null : $collection->url(1),
                'last' => $collection->url($collection->lastPage()),
                'prev' => $collection->previousPageUrl(),
                'next' => $collection->nextPageUrl(),
            ],
            'meta' => [
                'currentPage' => $collection->currentPage(),
                'from' => $collection->firstItem(),
                'lastPage' => $collection->lastPage(),
                'perPage' => $collection->perPage(),
                'to' => $collection->lastItem(),
                'total' => $collection->total(),
            ]
        ];
    }
}
