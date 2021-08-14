<?php

namespace lowebf\Persistance;

use lowebf\Environment;

class PersistorYaml implements IPersistance
{
    /* @var PersistorYaml|null */
	    private static $instance = null;

    public static function getInstance() : PersistorYaml
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function load(Environment $env, string $path) : array {}

    public function save(Environment $env, string $path, array $data) {}
}
