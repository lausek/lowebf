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

        $post = $env->posts()->load("2021-01-01-a")->unwrap();

        $this->assertSame("gustav", $post->getAuthor());
        $this->assertSame("<p>news goes here</p>", $post->getContent());
    }

    public function testJustContent()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/posts/2021-01-01-a.md", "news goes here");

        $post = $env->posts()->load("2021-01-01-a")->unwrap();

        $this->assertSame("news goes here", $post->getContentRaw());
    }

    public function testHorizontalLine()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/posts/2021-01-01-a.md", "---\n---\n# Title\n---\n");

        $post = $env->posts()->load("2021-01-01-a")->unwrap();

        $this->assertSame("<h1>Title</h1>\n\n<hr />", $post->getContent());
    }

    public function testHorizontalLineWithMetaInformationen()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/posts/2021-01-01-a.md", "---\nauthor: bernd\n---\n---\n");

        $post = $env->posts()->load("2021-01-01-a")->unwrap();

        $this->assertSame("bernd", $post->getAuthor());
        $this->assertSame("<hr />", $post->getContent());
    }

    public function testVideoEmbedding()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/media/vid/greet.mp4", "");
        $env->saveFile("/ve/data/posts/2021-01-01-a.md", "---\n---\n![Greet Video](/media/vid/greet.mp4)");

        $post = $env->posts()->load("2021-01-01-a")->unwrap();

        $this->assertSame(
            "<p><center><video controls><source src=\"/route.php?x=/media/vid/greet.mp4\" type=\"video/mp4\">Your browser does not support the video tag.</video></center></p>",
            $post->getContent()
        );
    }

    public function testImageEmbedding()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/media/img/a.png", "");
        $env->saveFile("/ve/data/posts/2021-01-01-a.md", "---\n---\n![A Picture](/media/img/a.png)");

        $post = $env->posts()->load("2021-01-01-a")->unwrap();

        $this->assertSame("<p><img src=\"/route.php?x=/media/img/a.png\" alt=\"A Picture\" /></p>", $post->getContent());
    }
}
