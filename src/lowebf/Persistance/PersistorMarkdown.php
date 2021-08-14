<?php

namespace lowebf\Persistance;

use lowebf\Environment;

class PersistorMarkdown implements IPersistance
{
    /* @var PersistorMarkdown|null */
	    private static $instance = null;

    public static function getInstance() : PersistorMarkdown
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function load(Environment $env, string $path) : array {
        $rawContent = $env->loadFile($path);

        return [];
    }

    public function save(Environment $env, string $path, array $data) {
        $lines = [];
        $lines[] = "---";

        foreach($data as $key => $value) {
            if($key === "content") {
                continue;
            }

            $lines[] = "$key: $value";
        }

        $lines[] = "---";

        if(isset($data["content"])) {
            $lines[] = $data["content"];
        }

        $serializedContent = implode($lines, "\n");

        $env->saveFile($path, $serializedContent);
    }
}
