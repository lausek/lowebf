<?php

namespace lowebf\Persistance;

use lowebf\Environment;
use lowebf\Error\InvalidFileFormatException;

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

    public function load(Environment $env, string $path) : array
    {
        $rawContent = $env->loadFile($path);
        $content = json_decode($rawContent, true);

        if ($content === null) {
            throw new InvalidFileFormatException(json_last_error_msg());
        }

        return $content;
    }

    public function save(Environment $env, string $path, array $data)
    {
        $serializedContent = json_encode($data, JSON_PRETTY_PRINT);
        $env->saveFile($path, $serializedContent);
    }
}
