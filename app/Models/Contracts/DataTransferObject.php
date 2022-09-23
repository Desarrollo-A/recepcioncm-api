<?php

namespace App\Models\Contracts;

use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Message;

trait DataTransferObject
{
    /**
     * @throws CustomErrorException
     */
    public function init(array $data)
    {
        foreach($data as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new CustomErrorException(Message::getMessageNotFieldClass($key, get_class($this)), 400);
            }
            $this->{$key} = $value;
        }
    }

    /**
     * @throws CustomErrorException
     */
    public function toArray($fields = []): array
    {
        $vars = get_object_vars($this);
        if (count($fields) === 0) {
            return $vars;
        }

        $output = [];
        foreach($fields as $field) {
            try {
                $output[$field] = $vars[$field];
            } catch (\Exception $ex) {
                throw new CustomErrorException(Message::getMessageNotFieldClass($field, get_class($this)), 400);
            }
        }

        return $output;
    }
}