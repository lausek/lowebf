<?php

namespace lowebf;

use lowebf\Error\FileNotFoundException;
use lowebf\Filesystem\VirtualFilesystem;
use lowebf\PhpRuntime;

class VirtualPhpRuntime extends PhpRuntime
{
    public function exitRuntime() {
        // avoid exit being called while testing
    }
}

class VirtualEnvironment extends Environment
{
    public function __construct(string $dir = "/ve")
    {
        parent::__construct($dir);

        $this->filesystem = new VirtualFilesystem($this);
        $this->phpRuntime = new VirtualPhpRuntime();
    }
}
