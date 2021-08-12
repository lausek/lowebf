<?php

namespace lowebf\Persistance;

class PersistorYaml implements IPersistance {

    /* @var PersistorYaml|null */
	private static $instance = null;

    public static function getInstance(): PersistorYaml {
        if(self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

	public function load(string $path): array {}

	public function save(string $path, array $data) {}
}
