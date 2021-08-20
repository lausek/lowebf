<?php

namespace lowebf\Module;

use lowebf\Error\FileNotFoundException;

// TODO: create cache directory if it does not exist yet
class CacheModule extends Module
{
    public function getPath(string $subpath = null) : string
    {
        if ($subpath !== null) {
            $subpath = ltrim($subpath, "/");
            return $this->env->asAbsolutePath("cache/$subpath");
        } else {
            return $this->env->asAbsolutePath("cache");
        }
    }

    public function exists(string $key) : bool
    {
        $path = $this->getPath($key);
        return $this->env->hasFile($path);
    }

    public function get(string $key)
    {
        try {
            $path = $this->getPath($key);
            return $this->env->loadFile($path);
        } catch (FileNotFoundException $e) {}

        return null;
    }

    public function set(string $key, $value)
    {
        $path = $this->getPath($key);
        $this->env->saveFile($path, $value);
    }

    public function clear(string $key) {}
}
