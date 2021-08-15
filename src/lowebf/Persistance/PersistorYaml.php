<?php

namespace lowebf\Persistance;

use lowebf\Environment;

use Spyc;

class PersistorYaml implements IPersistance
{
    /** @var PersistorYaml|null */
	    private static $instance = null;

    public static function getInstance() : PersistorYaml
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function load(Environment $env, string $path) : array
    {
        $rawContent = $env->loadFile($path);
        return Spyc::YAMLLoadString($rawContent);
    }

    public function save(Environment $env, string $path, array $data)
    {
        $serializedContent = Spyc::YAMLDump($data);
        $env->saveFile($path, $serializedContent);
    }
}
