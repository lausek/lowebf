<?php

namespace lowebf;

class Cache {
    public $path;

    public function __construct(string $path)
    {
        $this->path = $path;

        if(!is_dir($path))
        {
            mkdir($path);
        }

        if(!is_dir("$path/thumbs"))
        {
            mkdir("$path/thumbs");
        }
    }

    public function put(string $name, string $group, string $content): bool
    {
        return false;
    }

    public function load(string $name, string $group): bool
    {
        return false;
    }
}
