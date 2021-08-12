<?php

namespace lowebf\Data;

trait StorableTrait {
    public function get(string $name) {
        return $this->__get($name);
    }

    public function exists(string $name): bool {
        return $this->__isset($name);
    }

    public function set(string $name, $value) {
        $this->__set($name, $value);
    }

    public function unset(string $name) {
        $this->__unset($name);
    }
}
