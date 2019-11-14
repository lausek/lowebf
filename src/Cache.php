<?php

declare(strict_types=1);

namespace lowebf;

function create_dir_lazy(string $path): void
{
    if (! is_dir($path)) {
        mkdir($path);
    }
}

class Cache
{
    public $path;

    public function __construct(string $path)
    {
        $this->path = $path;

        create_dir_lazy($path);
        create_dir_lazy("${path}/thumbs");
        create_dir_lazy("${path}/css");
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
