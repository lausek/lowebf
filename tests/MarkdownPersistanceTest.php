<?php

namespace lowebf\Test;

require_once("util.php");

use lowebf\Data\Post;
use lowebf\Environment;
use lowebf\Persistance\PersistorMarkdown;
use lowebf\VirtualEnvironment;
use PHPUnit\Framework\TestCase;

final class MarkdownPersistanceTest extends TestCase
{
    public function testMetaInformation()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/posts/2021-01-01-a.md", "---\nauthor: gustav\n---\nnews goes here");

        $post = $env->posts()->load("2021-01-01-a");

        $this->assertSame("gustav", $post->getAuthor());
        $this->assertSame("<p>news goes here</p>", $post->getContent());
    }

    public function testJustContent()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/posts/2021-01-01-a.md", "news goes here");

        $post = $env->posts()->load("2021-01-01-a");

        $this->assertSame("news goes here", $post->getContentRaw());
    }
}
