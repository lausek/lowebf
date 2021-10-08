<?php

namespace lowebf\Module;

use lowebf\Data\ContentUnit;
use lowebf\Data\FrameworkConfig;
use lowebf\Environment;

class ConfigModule extends Module
{
    /** @var ContentUnit */
	    private $contentUnit;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $dataPath = $this->env->asAbsoluteDataPath("");
        $configPath = $this->env->findWithoutFileExtension(
            $dataPath,
            "config"
        )->unwrapOr(null);

        if ($configPath === null) {
            // create data directory if it does not exist
            if (!$this->env->hasFile($dataPath)) {
                $this->env->filesystem()->mkdir($dataPath);
            }

            $configPath = $this->env->asAbsoluteDataPath("config.yaml");
        }

        $this->contentUnit = ContentUnit::loadFromFileOrCreate($env, $configPath);
    }

    public function __isset(string $name) : bool
    {
        return $this->exists($name);
    }

    public function __get(string $name)
    {
        return $this->get($name);
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

    public function lowebf() : FrameworkConfig
    {
        $data = &$this->contentUnit->getReferenceOrInsert("lowebf", []);

        return new FrameworkConfig($data);
    }
}
