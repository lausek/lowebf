<?php

namespace lowebf\Data;

trait StorableTrait {
    public function get(string $name): mixed {
        return parent::__get($name);
    }

    public function exists(string $name): bool {
        return parent::__isset($name);
    }

    public function set(string $name, mixed $value) {
        parent::__set($name, $value);
    }

    public function unset(string $name) {
        parent::__unset($name);
    }
}
