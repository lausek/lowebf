<?php

namespace lowebf\Test;

require_once("util.php");

use lowebf\Environment;
use lowebf\VirtualEnvironment;
use lowebf\Data\Post;
use lowebf\Error\InvalidFileFormatException;
use lowebf\Persistance\PersistorMarkdown;
use PHPUnit\Framework\TestCase;

final class MarkdownPersistanceTest extends TestCase
{
    public function testInvalidLoading()
    {
        $this->expectException(InvalidFileFormatException::class);
        PersistorMarkdown::getInstance()->parseMarkdown("---");
    }

    public function testInvalidLoadingFull()
    {
        $postFilePath = "/ve/data/posts/2021-01-02-ab-c-d.md";

        $env = new VirtualEnvironment("/ve");
        $env->saveFile($postFilePath, "abc");

        $this->expectException(InvalidFileFormatException::class);

        // validation happens when the file is accessed
        $env->posts()->load("2021-01-02-ab-c-d")->getContent();
    }
}
