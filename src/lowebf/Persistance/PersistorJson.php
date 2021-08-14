<?php

namespace lowebf\Persistance;

use lowebf\Environment;

class PersistorJson implements IPersistance
{
    /* @var PersistorJson|null */
	    private static $instance = null;

    public static function getInstance() : PersistorJson
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function load(Environment $env, string $path) : array {}

    public function save(Environment $env, string $path, array $data) {}
}
