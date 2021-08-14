<?php

namespace lowebf\Error;

class FileNotFoundException extends \Exception
{
    public function __construct(string $path) {
        parent::__construct("file not found: $path");
    }
}
