<?php

namespace lowebf;

use lowebf\Error\FileNotFoundException;

class VirtualEnvironment extends Environment
{
    /** @var array */
    private $fileSystem = [];

    public function __construct(string $dir) {
        parent::__construct($dir);
    }

    public function asRealpath(string $path) : string {
        return $path;
    }

    public function loadFile(string $path) : string {
        //$present= isset($this->fileSystem[$path]) ? "true" : "false";
        //echo "looking for $path: $present". "\n";

        if(!isset($this->fileSystem[$path])) {
            throw new FileNotFoundException($path);
        }

        return $this->fileSystem[$path];
    }

    public function saveFile(string $path, $content) {
        //echo "persisting $path: $content". "\n";
        $this->fileSystem[$path] = $content;
    }

    public function & getFileSystem() : array {
        return $this->fileSystem;
    }

    public function listDirectory(string $path, bool $recursive = false) : array {

    }

    public function hasFile(string $path) : bool {
        return isset($this->fileSystem[$path]);
    }
}
