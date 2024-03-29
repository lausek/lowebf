<?php

namespace lowebf\Filesystem;

use lowebf\Environment;
use lowebf\Result;

abstract class CoreFilesystem
{
    /** @var Environment */
    protected $env;

    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    //public function copy(string $originFile, string $targetFile, bool $overwriteNewerFiles = false);

    public abstract function mkdir($dirs, int $mode = 0755);

    public abstract function exists($files) : bool;

    //public function touch($files, int $time = null, int $atime = null);

    public abstract function remove($files);

    //public function chown($files, $user, bool $recursive = false);

    //public function chgrp($files, $group, bool $recursive = false);
    
    //public function rename(string $origin, string $target, bool $overwrite = false);

    //public function symlink(string $originDir, string $targetDir, bool $copyOnWindows = false);

    //public function hardlink(string $originFile, $targetFiles);

    //public function readlink(string $path, bool $canonicalize = false);

    //public function makePathRelative(string $endPath, string $startPath);

    //public function mirror(string $originDir, string $targetDir, \Traversable $iterator = null, array $options = []);

    //public function isAbsolutePath(string $file);

    //public function tempnam(string $dir, string $prefix/*, string $suffix = ''*/);

    public abstract function lastModified(string $filename) : int;

    /** 
    * @return Result<array> Result type: An array where the key is the relative path to the file/directory and the value is either a string containing the absolute path or an array if the path is a directory */
    public abstract function listDirectory(string $filename) : Result;

    /** 
    * @return Result<array> Result type: An array where the key is the relative path to the file/directory and the value is either a string containing the absolute path or an array if the path is a directory */
    public abstract function listDirectoryRecursive(string $filename) : Result;

    /**
    * @return Result<string>
    * */
    public abstract function loadFile(string $filename) : Result;

    public abstract function saveFile(string $filename, $content);

    public abstract function sendFile(string $filename);

    //public function appendToFile(string $filename, $content);
}
