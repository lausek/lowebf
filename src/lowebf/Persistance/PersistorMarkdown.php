<?php

namespace lowebf\Persistance;

class PersistorMarkdown implements IPersistance {

	private ?PersistorMarkdown $instance = null;

    public static function getInstance(): PersistorMarkdown {
        if(self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

	public function load(string $path): array {}

	public function save(string $path, array $data) {}
}
