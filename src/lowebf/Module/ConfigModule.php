<?php

namespace lowebf\Module;

use lowebf\Environment;
use lowebf\Data\ContentUnit;
use lowebf\Data\StorableTrait;

class ConfigModule extends Module {
    use StorableTrait;

    /* @var ContentUnit */
	private $contentUnit;

    public function __construct(Environment $env) {
        parent::__construct($env);

        $configPath = $this->env->asAbsoluteDataPath("config.json");
        $this->contentUnit = ContentUnit::loadFileOrCreate($configPath);
    }

    public function __get(string $name) {
        return $this->contentUnit->get($name);
    }

    public function __set(string $name, $value) {
        $this->contentUnit->set($name, $value);
    }

    public function __isset(string $name): bool {
        return $this->contentUnit->exists($name);
    }

    public function __unset(string $name) {
        $this->contentUnit->unset($name);
    }
}
