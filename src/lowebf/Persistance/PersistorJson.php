<?php

namespace lowebf\Persistance;

use lowebf\Environment;
use lowebf\Error\InvalidFileFormatException;
use lowebf\Result;

class PersistorJson implements IPersistance
{
    /** @var PersistorJson|null */
	    private static $instance = null;

    public static function getInstance() : PersistorJson
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

        $content = json_decode($result->unwrap(), true);

        if ($content === null) {
            $jsonError = json_last_error_msg();
            $e = new InvalidFileFormatException("$path: $jsonError");
            return Result::error($e);
        }

        return Result::ok($content);
    }

    public function save(Environment $env, string $path, array $data)
    {
        $serializedContent = json_encode($data, JSON_PRETTY_PRINT);
        $env->saveFile($path, $serializedContent);
    }
}
