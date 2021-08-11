<?php

namespace lowebf\Persistance;

class PersistorJson implements IPersistance {

	private static ?PersistorJson $instance = null;

    public static function getInstance(): PersistorJson {
        if(self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

	public function load(string $path): array {}

	public function save(string $path, array $data) {}
}
