<?php

namespace lowebf\Persistance;

use lowebf\Environment;
use lowebf\Error\InvalidFileFormatException;

use \Spyc;

class PersistorMarkdown implements IPersistance
{
    /** @var PersistorMarkdown|null */
	    private static $instance = null;

    public static function getInstance() : PersistorMarkdown
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function parseMarkdown(string $rawContent)
    {
        if (!preg_match('/---\s([\s\S]*)---\s?([\s\S]*)/m', $rawContent, $matches)) {
            throw new InvalidFileFormatException("invalid markdown file format.");
        }

        return $matches;
    }

    public function load(Environment $env, string $path) : array
    {
        $rawContent = $env->loadFile($path);

        $matches = $this->parseMarkdown($rawContent);

        $parsed = Spyc::YAMLLoadString($matches[1]);
        $parsed["content"] = ltrim($matches[2], "\n ");

        return $parsed;
    }

    public function save(Environment $env, string $path, array $data)
    {
        $lines = [];
        $lines[] = "---";

        foreach ($data as $key => $value) {
            if ($key === "content") {
                continue;
            }

            $lines[] = "$key: $value";
        }

        $lines[] = "---\n";

        if (isset($data["content"])) {
            $lines[] = $data["content"];
        }

        $serializedContent = implode($lines, "\n");

        $env->saveFile($path, $serializedContent);
    }
}
