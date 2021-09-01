<?php

namespace lowebf\Filesystem;

use lowebf\Error\FileNotFoundException;

class VirtualFilesystem extends CoreFilesystem
{
    /** @var array */
    protected $filesystem = [];

    public function &asArray() : array
    {
        return $this->filesystem;
    }

    public function mkdir($dirs, int $mode = 0755)
    {
        $completePath = "";

        // TODO: allow backslash too
        foreach (explode("/", $dirs) as $part) {
            $completePath .= $part;

            if (!empty($completePath)) {
                $this->filesystem[$completePath] = [];
            }

            $completePath .= "/";
        }
    }

    public function exists($files) : bool
    {
        foreach ($this->filesystem as $fullPath => $_) {
            if (0 === strpos($fullPath, $files)) {
                return true;
            }
        }

        return false;
    }

    public function remove($files) {}

    public function lastModified(string $filename) : int
    {
        return 0;
    }

    public function listDirectory(string $filename) : array
    {
        $path = rtrim($filename, "/");
        $filtered = [];

        if (!$this->exists($path)) {
            throw new FileNotFoundException($path);
        }

        foreach ($this->filesystem as $key => $value) {
            $keyWithoutParent = substr($key, strlen($path) + 1);

            if ($keyWithoutParent === false || empty($keyWithoutParent)) {
                continue;
            }

            //$dirName = pathinfo($keyWithoutParent, PATHINFO_DIRNAME);
            if (is_array($value)) {
                // as directories are accessible with trailing slash, check
                // if it was already added to the list
                $normalizedDirectoryName = rtrim($keyWithoutParent, "/");
                if (isset($filtered[$normalizedDirectoryName])) {
                    continue;
                }

                // always add directory with trailing slash
                $filtered[$normalizedDirectoryName] = [];
            } else {
                $filtered[$keyWithoutParent] = $key;
            }
        }

        return $filtered;
    }

    private function listDirectoryRecursiveInner(string $filename, int $maxDepth, int $currentDepth) : array
    {
        $currentDepth += 1;

        if ($maxDepth < $currentDepth) {
            return [];
        }

        $path = rtrim($filename, "/");
        $filtered = [];

        if (!$this->exists($path)) {
            throw new FileNotFoundException($path);
        }

        foreach ($this->filesystem as $key => $value) {
            if (strpos($key, $path) !== 0) {
                continue;
            }

            $keyWithoutParent = substr($key, strlen($path) + 1);
            $keyWithoutParent = rtrim($keyWithoutParent, "/");

            if ($keyWithoutParent === false || empty($keyWithoutParent)) {
                continue;
            }

            // check if $key has an upper directory. if it does have one
            // it is nested, so we won't check it here.
            if (dirname($keyWithoutParent) !== ".") {
                continue;
            }

            // if $key is a directory -> add nested files and directories
            if (is_array($value)) {
                $directoryPath = "$path/$keyWithoutParent";
                $filtered[$keyWithoutParent] = $this->listDirectoryRecursiveInner($directoryPath, $maxDepth, $currentDepth);
            } else {
                $filtered[$keyWithoutParent] = "$path/$keyWithoutParent";
            }
        }

        $currentDepth -= 1;

        return $filtered;
    }

    public function listDirectoryRecursive(string $filename, int $depth = PHP_INT_MAX) : array
    {
        return $this->listDirectoryRecursiveInner($filename, $depth, 0);
    }

    public function loadFile(string $filename) : string
    {
        if (!isset($this->filesystem[$filename])) {
            throw new FileNotFoundException($filename);
        }

        return $this->filesystem[$filename];
    }

    public function saveFile(string $filename, $content)
    {
        $directoryPath = dirname($filename);

        if ($directoryPath !== "") {
            $this->mkdir($directoryPath);
        }

        $this->filesystem[$filename] = $content;
    }

    public function sendFile(string $filename) {}
}
