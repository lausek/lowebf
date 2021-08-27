<?php

namespace lowebf\Filesystem;

use lowebf\Error\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class Filesystem implements IFilesystem
{
    /** @var Filesystem */
    protected $filesystem;

    public function __construct()
    {
        $this->filesystem = new SymfonyFilesystem();
    }

    public function mkdir($dirs, int $mode = 0755)
    {
        //$directoryName = pathinfo($path, PATHINFO_DIRNAME);
        //@mkdir($directoryName, 0755, true);
        $this->filesystem->mkdir($dirs, $mode);
    }

    public function exists($files) : bool
    {
        return $this->filesystem->exists($files);
    }

    public function remove($files)
    {
        $this->filesystem->remove($files);
    }

    public function lastModified(string $filename) : int
    {
        return filemtime($filename);
    }

    public function listDirectory(string $filename) : ?array
    {
        $files = [];

        if (!$this->exists($path)) {
            return null;
        }

        foreach (scandir($filename) as $childPath) {
            if ($childPath === "." || $childPath === "..") {
                continue;
            }

            $childPathAbsolute = "$path/$childPath";

            if (is_dir($childPathAbsolute)) {
                if ($recursive) {
                    foreach ($this->listDirectory($childPathAbsolute, true) as $dirChildRelative => $dirChildAbsolute) {
                        $relativePath = "$childPath/$dirChildRelative";
                        $files[$relativePath] = "$childPathAbsolute/$dirChildAbsolute";
                    }
                }
            } else {
                $files[$childPath] = $childPathAbsolute;
            }
        }

        return $files;
    }

    public function loadFile(string $filename) : string
    {
        $content = @file_get_contents($filename);

        if ($content === false) {
            throw new FileNotFoundException($filename);
        }

        return $content;
    }

    public function saveFile(string $filename, $content)
    {
        $this->filesystem->dumpFile($filename, $content);
    }

    public function sendFile(string $filename)
    {
        readfile($filename);
    }
}
