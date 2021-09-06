<?php

namespace lowebf\Error;

class ConversionException extends \Exception
{
    public function __construct(string $fromType, string $toType, $value)
    {
        parent::__construct("cannot convert from $fromType to $toType: " . print_r($value, true));
    }
}
