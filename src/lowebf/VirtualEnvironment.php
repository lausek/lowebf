<?php

namespace lowebf;

use lowebf\Error\FileNotFoundException;
use lowebf\Filesystem\VirtualFilesystem;

class VirtualEnvironment extends Environment
{
    public function __construct(string $dir)
    {
        parent::__construct($dir);

        $this->filesystem = new VirtualFilesystem();
    }

    public function asRealpath(string $path) : string
    {
        return $path;
    }

    public function getLastModified(string $path) : int
    {
        return $this->filesystem->lastModified($path);
    }

    public function hasFile(string $path) : bool
    {
        return $this->filesystem->exists($path);
    }

    public function loadFile(string $path) : string
    {
        return $this->filesystem->loadFile($path);
    }

    public function saveFile(string $path, $content)
    {
        $this->filesystem->saveFile($path, $content);
    }

    public function &getFileSystem() : array
    {
        return $this->filesystem->asArray();
    }

    public function listDirectory(string $path, bool $recursive = false) : ?array
    {
        return $this->filesystem->listDirectory($path, $recursive);
    }
}
