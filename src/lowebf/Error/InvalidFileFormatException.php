<?php

namespace lowebf\Error;

class InvalidFileFormatException extends \Exception
{
    public function __construct(string $error = null)
    {
        parent::__construct("file format error: $error");
    }
}
