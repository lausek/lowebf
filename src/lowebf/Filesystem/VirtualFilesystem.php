<?php

namespace lowebf\Filesystem;

class VirtualFilesystem extends CoreFilesystem
{
    /** @var array */
    protected $filesystem = [];

    public function &asArray() : array
    {
        return $this->filesystem;
    }

    public function mkdir($dirs, int $mode = 0755) {}

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

    public function listDirectory(string $filename) : ?array
    {
        $path = rtrim($filename, "/");
        $filtered = [];

        if (!$this->exists($path)) {
            return null;
        }

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

    public function loadFile(string $filename) : string
    {
        if (!isset($this->filesystem[$path])) {
            throw new FileNotFoundException($path);
        }

        return $this->filesystem[$path];
    }

    public function saveFile(string $filename, $content)
    {
        $this->filesystem[$filename] = $content;
    }

    public function sendFile(string $filename) {}
}
