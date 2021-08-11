<?php

namespace lowebf\Module;

use lowebf\Data\StorableTrait;

class ConfigModule {
    use StorableTrait;

    /* @var Environment */
	private $environment;
    /* @var ContentUnit */
	private $contentUnit;

    public function __get(string $name): mixed {
        return $this->contentUnit->get($name);
    }

    public function __set(string $name, mixed $value) {
        $this->contentUnit->set($name, $value);
    }

    public function __isset(string $name): bool {
        return $this->contentUnit->exists($name);
    }

    public function __unset(string $name) {
        $this->contentUnit->unset($name);
    }
}
