<?php

namespace lowebf\Module;

use lowebf\Environment;
use lowebf\Data\ContentUnit;
use lowebf\Data\IStorable;

class ConfigModule extends Module implements IStorable
{
    /** @var ContentUnit */
	    private $contentUnit;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $configPath = $this->env->findWithoutFileExtension(
            $this->env->asAbsoluteDataPath(""),
            "config"
        );

        if($configPath === null) {
            $configPath = $this->env->asAbsoluteDataPath("config.yaml");
        }

        $this->contentUnit = ContentUnit::loadFromFileOrCreate($env, $configPath);
    }

    public function get(string $name, $default = null)
    {
        return $this->contentUnit->get($name, $default);
    }

    public function set(string $name, $value)
    {
        $this->contentUnit->set($name, $value);
    }

    public function exists(string $name) : bool
    {
        return $this->contentUnit->exists($name);
    }

    public function unset(string $name) {
        $this->contentUnit->unset($name);
    }

    public function save()
    {
        $this->contentUnit->save();
    }

    public function isCacheEnabled() : bool
    {
        return $this->get("cacheEnabled", true) === true;
    }

    public function isDebugEnabled() : bool
    {
        return $this->get("debugEnabled", false) === true;
    }
}
