<?php

namespace lowebf\Persistance;

use lowebf\Environment;
use lowebf\Error\InvalidFileFormatException;
use lowebf\Result;

use Michelf\Markdown;
use Spyc;

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

    public function extractMetaInformation(string $rawContent) : ?array
    {
        $matches = [];
        $parts = explode("---\n", $rawContent);

        if (count($parts) < 3) {
            return null;
        }

        $matches[] = implode("\n", array_slice($parts, 0, 2));
        $matches[] = implode("\n---\n", array_slice($parts, 2));

        return $matches;
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
        $rawContent = $result->unwrap();

        $matches = $this->extractMetaInformation($rawContent);

        if ($matches !== null) {
            $parsed = Spyc::YAMLLoadString($matches[0]);
            $parsed["content"] = ltrim($matches[1], "\n ");
        } else {
            $parsed["content"] = $rawContent;
        }

        return Result::ok($parsed);
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
