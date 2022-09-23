<?php

namespace App\Models\Traits;

use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Message;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;

trait Sortable
{
    /**
     * @throws CustomErrorException
     */
    public function scopeApplySort(Builder $query, string $sort = null): Builder
    {
        if (!property_exists($this, 'allowedSorts')) {
            throw new CustomErrorException(Message::getMessageHasNotAllowedSorts(get_class($this)), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (is_null($sort)) {
            return $query;
        }

        $sortFields = explode(',', $sort);

        foreach ($sortFields as $sortField) {
            $direction = 'ASC';

            if (str_starts_with($sortField, '-')) {
                $direction = 'DESC';
                $sortField = substr($sortField, 1);
            }

            if (!collect($this->allowedSorts)->contains($sortField)) {
                throw new CustomErrorException(Message::INVALID_QUERY_PARAMETER, Response::HTTP_BAD_REQUEST);
            }

            $query->orderBy($sortField, $direction);
        }

        return $query;
    }
}