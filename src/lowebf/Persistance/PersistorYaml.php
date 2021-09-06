<?php

namespace lowebf\Persistance;

use lowebf\Environment;
use lowebf\Result;

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

    /**
     * @return Result<string>
     * */
    public function load(Environment $env, string $path) : Result
    {
        $result = $env->loadFile($path);
        if ($result->isError()) {
            return $result;
        }

        $content = Spyc::YAMLLoadString($result->unwrap());
        return Result::ok($content);
    }

    public function save(Environment $env, string $path, array $data)
    {
        $serializedContent = Spyc::YAMLDump($data);
        $env->saveFile($path, $serializedContent);
    }
}
