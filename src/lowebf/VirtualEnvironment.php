<?php

namespace lowebf;

use lowebf\Error\FileNotFoundException;
use lowebf\Filesystem\VirtualFilesystem;

class VirtualEnvironment extends Environment
{
    public function __construct(string $dir)
    {
        parent::__construct($dir);

        $this->filesystem = new VirtualFilesystem($this);
    }
}
