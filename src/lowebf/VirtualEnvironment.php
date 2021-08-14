<?php

namespace lowebf;

class VirtualEnvironment extends Environment
{
    private $fileSystem = [];

    public function __construct(string $dir) {
        parent::__construct($dir);
    }

    public function asRealpath(string $path) : string {
        return $path;
    }

    public function loadFile(string $path) {
        return $this->fileSystem[$path];
    }

    public function saveFile(string $path, $content) {
        $this->fileSystem[$path] = $content;
    }

    public function getFileSystem() : array {
        return $this->fileSystem;
    }

    public function listDirectory(string $path) : array {

    }

    public function hasFile(string $path) : bool {
        return isset($this->fileSystem[$path]);
    }
}
