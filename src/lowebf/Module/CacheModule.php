<?php

namespace lowebf\Module;

use lowebf\Error\FileNotFoundException;

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
        try {
            $this->getPath($key);
            return true;
        } catch (FileNotFoundException $e) {
            return false;
        }
    }

    public function get(string $key)
    {
        try {
            $path = $this->env->getPath($key);
            return $this->env->loadFile($path);
        } catch (FileNotFoundException $e) {}

        return null;
    }

    public function set(string $key, $value)
    {
        $path = $this->env->getPath($key);
        $this->env->saveFile($path, $value);
    }

    public function clear(string $key) {}
}
