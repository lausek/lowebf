<?php

namespace lowebf\Test;

require_once("util.php");

use lowebf\Environment;
use lowebf\VirtualEnvironment;
use lowebf\Data\Post;
use lowebf\Error\InvalidFileFormatException;
use lowebf\Persistance\IPersistance;
use PHPUnit\Framework\TestCase;

final class PostTest extends TestCase
{
    public function testSavingPost()
    {
        $postFilePath = "/ve/data/posts/2021-01-02-ab-c-d.md";

        $env = new VirtualEnvironment("/ve");

        $env->posts()->loadOrCreate("2021-01-02-ab-c-d");

        $this->assertTrue($env->hasFile($postFilePath));
    }

    public function testSetAttributesFromPath()
    {
        $postFilePath = "/ve/data/posts/2021-01-02-ab-c-d.md";

        $env = new VirtualEnvironment("/ve");

        $env->posts()->loadOrCreate("2021-01-02-ab-c-d");

        $post = $env->posts()->loadOrCreate("2021-01-02-ab-c-d");

        $this->assertSame("2021-01-02", $post->getDate()->format("Y-m-d"));
        $this->assertSame("Ab C D", $post->getTitle());
        $this->assertSame(null, $post->getAuthor());
        $this->assertTrue($env->hasFile($postFilePath));
    }

    public function testSavingContent()
    {
        $postFilePath = "/ve/data/posts/2021-01-02-ab-c-d.md";

        $env = new VirtualEnvironment("/ve");

        $env->posts()->loadOrCreate("2021-01-02-ab-c-d")
            ->setAuthor("root")
            ->setContent("TestContent **big**")
            ->save();

        $post = $env->posts()->loadOrCreate("2021-01-02-ab-c-d");

        $this->assertSame("2021-01-02", $post->getDate()->format("Y-m-d"));
        $this->assertSame("Ab C D", $post->getTitle());
        $this->assertSame("root", $post->getAuthor());
        $this->assertSame("TestContent **big**", $post->getContent());
        $this->assertSame("<p>TestContent <strong>big</strong></p>", $post->getContentHtml());
        $this->assertTrue($env->hasFile($postFilePath));
    }

    public function testLazyLoading()
    {
        $postFilePath = "/ve/data/posts/2021-01-02-ab-c-d.md";

        $env = $this->getMockBuilder(VirtualEnvironment::class)
            ->setConstructorArgs(["/ve"])
            ->setMethodsExcept(["hasFile", "saveFile", "posts"])
            ->getMock();

        $env->saveFile($postFilePath, "");

        $env->expects($this->once())
            ->method("asAbsoluteDataPath")
            ->will($this->returnValue($postFilePath));

        $env->expects($this->never())
            ->method("loadFile")
            ->with($postFilePath)
            ->will($this->returnValue("---\n---\n"));

        $post = $env->posts()->load("2021-01-02-ab-c-d");
        $this->assertSame("2021-01-02", $post->getDate()->format("Y-m-d"));
        $this->assertSame("Ab C D", $post->getTitle());
    }

    public function testLazyLoadingContent()
    {
        $postFilePath = "/ve/data/posts/2021-01-02-ab-c-d.md";
        $postFileContent = "---\n---\nabc";

        $env = $this->getMockBuilder(VirtualEnvironment::class)
            ->setConstructorArgs(["/ve"])
            ->setMethodsExcept(["hasFile", "saveFile", "posts"])
            ->getMock();

        $env->saveFile($postFilePath, $postFileContent);

        $env->expects($this->once())
            ->method("asAbsoluteDataPath")
            ->will($this->returnValue($postFilePath));

        $env->expects($this->once())
            ->method("loadFile")
            ->with($postFilePath)
            ->will($this->returnValue($postFileContent));

        $post = $env->posts()->load("2021-01-02-ab-c-d");
        $this->assertSame("2021-01-02", $post->getDate()->format("Y-m-d"));
        $this->assertSame("Ab C D", $post->getTitle());
        $this->assertSame("abc", $post->getContent());
    }
}
