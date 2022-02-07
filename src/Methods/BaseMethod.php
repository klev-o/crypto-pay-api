<?php

namespace Klev\CryptoPayApi\Methods;

abstract class BaseMethod
{
    /**
     * Converting an object to an array, taking into account the API requirements for boolean values
     * @return array
     */
    public function toArray(): array
    {
        return array_map(function ($item) {
            if (is_bool($item)) {
                return $item ? "true" : "false";
            }
            return $item;
        }, get_object_vars($this));
    }
}