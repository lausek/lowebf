<?php

namespace lowebf\Filesystem;

use lowebf\Environment;
use lowebf\Error\FileNotFoundException;
use lowebf\Result;
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

    /**
     * @return Result<array>
     * */
    public function listDirectory(string $filename) : Result
    {
        $path = rtrim($filename, "/");
        $files = [];

        if (!$this->exists($filename)) {
            return Result::error(new FileNotFoundException($filename));
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

        return Result::ok($files);
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

        $result = $this->listDirectory($filename);
        if ($result->isError()) {
            return $result;
        }
        $files = $result->unwrap();

        foreach ($files as $relativePath => $value) {
            $childPathAbsolute = "$filename/$relativePath";

            if (is_array($value)) {
                $recursiveListingResult = $this->listDirectoryRecursiveInner($childPathAbsolute, $maxDepth, $currentDepth);

                if ($recursiveListingResult->isError()) {
                    return $recursiveListingResult;
                }

                $files[$relativePath] = $recursiveListingResult->unwrap();
            } else {
                $files[$relativePath] = $childPathAbsolute;
            }
        }

        $currentDepth -= 1;

        return Result::ok($files);
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
        $content = @file_get_contents($filename);

        if ($content === false) {
            return Result::error(new FileNotFoundException($filename));
        }

        return Result::ok($content);
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
