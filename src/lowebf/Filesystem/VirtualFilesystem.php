<?php

namespace lowebf\Filesystem;

use lowebf\Error\FileNotFoundException;
use lowebf\Result;

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

    /**
     * @return Result<array>
     * */
    public function listDirectory(string $filename) : Result
    {
        $path = rtrim($filename, "/");
        $filtered = [];

        if (!$this->exists($path)) {
            return Result::error(new FileNotFoundException($path));
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

        return Result::ok($filtered);
    }

    /**
     * @return Result<array>
     * */
    private function listDirectoryRecursiveInner(string $filename, int $maxDepth, int $currentDepth) : Result
    {
        $currentDepth += 1;

        if ($maxDepth < $currentDepth) {
            return Result::ok([]);
        }

        $path = rtrim($filename, "/");
        $filtered = [];

        if (!$this->exists($path)) {
            return Result::error(new FileNotFoundException($path));
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
                $recursiveListingResult = $this->listDirectoryRecursiveInner($directoryPath, $maxDepth, $currentDepth);
                if ($recursiveListingResult->isError()) {
                    return $recursiveListingResult;
                }

                $filtered[$keyWithoutParent] = $recursiveListingResult->unwrap();
            } else {
                $filtered[$keyWithoutParent] = "$path/$keyWithoutParent";
            }
        }

        $currentDepth -= 1;

        return Result::ok($filtered);
    }

    /**
     * @return Result<array>
     * */
    public function listDirectoryRecursive(string $filename, int $depth = PHP_INT_MAX) : Result
    {
        return $this->listDirectoryRecursiveInner($filename, $depth, 0);
    }

    /**
     * @return Result<string>
     * */
    public function loadFile(string $filename) : Result
    {
        if (!isset($this->filesystem[$filename])) {
            return Result::error(new FileNotFoundException($filename));
        }

        return Result::ok($this->filesystem[$filename]);
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
