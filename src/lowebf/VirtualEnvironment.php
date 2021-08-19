<?php

namespace lowebf;

use lowebf\Error\FileNotFoundException;

class VirtualEnvironment extends Environment
{
    /** @var array */
    private $fileSystem = [];

    public function __construct(string $dir)
    {
        parent::__construct($dir);
    }

    public function asRealpath(string $path) : string
    {
        return $path;
    }

    public function getLastModified(string $path) : int
    {
        return 0;
    }

    public function hasFile(string $path) : bool
    {
        return isset($this->fileSystem[$path]);
    }

    public function loadFile(string $path) : string
    {
        if (!isset($this->fileSystem[$path])) {
            throw new FileNotFoundException($path);
        }

        return $this->fileSystem[$path];
    }

    public function saveFile(string $path, $content)
    {
        $this->fileSystem[$path] = $content;
    }

    public function &getFileSystem() : array
    {
        return $this->fileSystem;
    }

    public function listDirectory(string $path, bool $recursive = false) : array
    {
        $path = rtrim($path, "/");
        $filtered = [];

        foreach ($this->fileSystem as $key => $value) {
            $keyWithoutParent = substr($key, strlen($path) + 1);

            if ($recursive) {
                if (!str_starts_with($key, $path)) {
                    continue;
                }
            } else {
                $dirName = pathinfo($keyWithoutParent, PATHINFO_DIRNAME);
                if ($dirName === "") {
                    continue;
                }
            }

            $filtered[$keyWithoutParent] = $key;
        }

        return $filtered;
    }
}
