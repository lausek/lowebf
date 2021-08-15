<?php

namespace lowebf\Module;

class CacheModule extends Module
{
    public function get(string $key) {}

    public function getPath() : string
    {
        return $this->env->asAbsolutePath("cache");
    }

    public function set(string $key, $value) {}

    public function clear(string $key) {}
}
