<?php

namespace lowebf\Filesystem;

use lowebf\Environment;
use lowebf\Error\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class Filesystem extends CoreFilesystem
{
    /** @var Filesystem */
    protected $filesystem;

    public function __construct(Environment $env)
    {
        parent::__construct($env);
        $this->filesystem = new SymfonyFilesystem();
    }

    public function &asArray() : array
    {
        throw new \Exception("not implemented");
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

    public function listDirectory(string $filename) : array
    {
        $path = rtrim($filename, "/");
        $files = [];

        if (!$this->exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        foreach (scandir($filename) as $relativePath) {
            if ($relativePath === "." || $relativePath === "..") {
                continue;
            }

            $absolutePath = "$path/$relativePath";

            if (is_dir($absolutePath)) {
                $files[$relativePath] = [];
            } else {
                $files[$relativePath] = $absolutePath;
            }
        }

        return $files;
    }

    public function listDirectoryRecursive(string $filename) : array
    {
        $files = $this->listDirectory($filename);

        foreach ($files as $relativePath => $value) {
            $childPathAbsolute = "$filename/$relativePath";

            if (is_array($value)) {
                $files[$relativePath] = $this->listDirectoryRecursive($childPathAbsolute);
            } else {
                $files[$relativePath] = $childPathAbsolute;
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
