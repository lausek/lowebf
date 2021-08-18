<?php

namespace lowebf\Twig;

use lowebf\Environment;

use \Twig\Cache\CacheInterface;
use \Twig\Cache\FilesystemCache;

class Cache implements CacheInterface
{
    /** @var Environment */
    private $env;

    /** @var string */
    private $path;

    /** @var FilesystemCache */
    private $twigCache;

    public function __construct(Environment $env)
    {
        $this->env = $env;
        $this->path = $env->asAbsolutePath("cache");
        $this->twigCache = new FilesystemCache($this->path);
    }
    /**
     * Generates a cache key for the given template class name.
     */
    public function generateKey(string $name, string $className) : string
    {
        return $this->twigCache->generateKey($name, $className);
    }

    /**
     * Writes the compiled template to cache.
     *
     * @param string $content The template representation as a PHP class
     */
    public function write(string $key, string $content) : void
    {
        $this->twigCache->write($key, $content);
    }

    /**
     * Loads a template from the cache.
     */
    public function load(string $key) : void
    {
        $this->twigCache->load($key);
    }

    /**
     * Returns the modification timestamp of a key.
     */
    public function getTimestamp(string $key) : int
    {
        return $this->twigCache->getTimestamp($key);
    }
}
