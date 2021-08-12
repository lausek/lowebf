<?php

namespace lowebf\Module;

class CacheModule extends Module
{
    public function get(string $key) : ?mixed {}

    public function getPath() : string
    {
        return $this->env->asAbsolutePath("cache");
    }

    public function set(string $key, mixed $value) {}

    public function clear(string $key) {}
}
