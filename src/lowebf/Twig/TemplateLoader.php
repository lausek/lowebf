<?php

namespace lowebf\Twig;

use lowebf\Environment;

use \Twig\Loader\LoaderInterface;
use \Twig\Source;

class TemplateLoader implements LoaderInterface
{
    /** @var Environment */
    private $env;

    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    public function getFilePath(string $name) : string
    {
        return $this->env->asAbsolutePath("site/template/$name");
    }

    /**
     * Returns the source context for a given template logical name.
     *
     * @throws LoaderError When $name is not found
     */
    public function getSourceContext(string $name) : Source
    {
        $path = $this->getFilePath($name);
        $content = $this->env->loadFile($path);
        return new Source($content, $name, $path);
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @throws LoaderError When $name is not found
     */
    public function getCacheKey(string $name) : string
    {
        return $name;
    }

    /**
     * @param int $time Timestamp of the last modification time of the cached template
     *
     * @throws LoaderError When $name is not found
     */
    public function isFresh(string $name, int $time) : bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function exists(string $name)
    {
        $path = $this->getFilePath($name);
        return $this->env->hasFile($path);
    }
}
