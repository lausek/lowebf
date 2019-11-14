<?php

namespace lowebf;

class Cache {
    public $path;

    public function __construct(string $path)
    {
        $this->path = $path;
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
