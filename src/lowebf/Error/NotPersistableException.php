<?php

namespace lowebf\Error;

class NotPersistableException extends \Exception
{
    public function __construct(string $fileExtension)
    {
        parent::__construct("cannot persist file of type $fileExtension");
    }
}
