<?php

namespace lowebf\Module;

use lowebf\Environment;
use lowebf\Data\ContentUnit;
use lowebf\Data\IStorable;

class ConfigModule extends Module implements IStorable
{
    /* @var ContentUnit */
	    private $contentUnit;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $configPath = $this->env->asAbsoluteDataPath("config.json");
        $this->contentUnit = ContentUnit::loadFileOrCreate($configPath);
    }

    public function get(string $name)
    {
        return $this->contentUnit->get($name);
    }

    public function set(string $name, $value)
    {
        $this->contentUnit->set($name, $value);
    }

    public function exists(string $name) : bool
    {
        return $this->contentUnit->exists($name);
    }

    public function unset(string $name)
    {
        $this->contentUnit->unset($name);
    }
}
