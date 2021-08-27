<?php

namespace lowebf\Filesystem;

interface IFilesystem
{
    //public function copy(string $originFile, string $targetFile, bool $overwriteNewerFiles = false);

    public function mkdir($dirs, int $mode = 0755);

    public function exists($files) : bool;

    //public function touch($files, int $time = null, int $atime = null);

    public function remove($files);

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

    public function lastModified(string $filename) : int;

    public function listDirectory(string $filename) : ?array;

    public function loadFile(string $filename) : string;

    public function saveFile(string $filename, $content);

    public function sendFile(string $filename);

    //public function appendToFile(string $filename, $content);
}
