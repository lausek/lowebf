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
        $this->assertSame("2021-01-02-ab-c-d", $post->getId());
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
        $this->assertSame("2021-01-02-ab-c-d", $post->getId());
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
            ->setMethods(["loadFile"])
            ->getMock();

        $env->saveFile($postFilePath, "");

        $env->expects($this->never())
            ->method("loadFile")
            ->with($postFilePath);

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
            ->setMethods(["loadFile"])
            ->getMock();

        $env->saveFile($postFilePath, $postFileContent);

        $env->expects($this->once())
            ->method("loadFile")
            ->with($postFilePath)
            ->will($this->returnValue($postFileContent));

        $post = $env->posts()->load("2021-01-02-ab-c-d");
        $this->assertSame("2021-01-02", $post->getDate()->format("Y-m-d"));
        $this->assertSame("Ab C D", $post->getTitle());
        $this->assertSame("abc", $post->getContent());
    }

    private function populateFileSystem(Environment $env, $files = null)
    {
        if ($files === null) {
            // save files in unsorted order
            $files = [
                "2021-08-01-a.md" => "",
                "2021-08-03-a.md" => "",
                "2021-08-01-b.md" => "",
                "2021-08-04-a.md" => "",
                "2020-08-01-c.md" => "",
            ];
        }

        $this->assertEmpty($env->getFileSystem());

        foreach ($files as $path => $content) {
            $env->saveFile("/ve/data/posts/$path", $content);
        }

        $this->assertNotEmpty($env->getFileSystem());
    }

    public function testLoadingPage()
    {
        $postDates = [];
        $postTitles = [];

        $env = new VirtualEnvironment("/ve");
        $this->populateFileSystem($env);
        $posts = $env->posts()->loadPage(1);

        foreach ($posts as $post) {
            $postDates[] = $post->getDate()->format("Y-m-d");
            $postTitles[] = $post->getTitle();
        }

        $this->assertSame(["2021-08-04", "2021-08-03", "2021-08-01", "2021-08-01", "2020-08-01"], $postDates);
        $this->assertSame(["A", "A", "B", "A", "C"], $postTitles);
    }

    public function testPaging()
    {
        $getTitle = function ($post) {
            return $post->getTitle();
        };

        $env = new VirtualEnvironment("/ve");
        $this->populateFileSystem($env);

        $env->posts()->setPostsPerPage(3);

        $pageOne = $env->posts()->loadPage(1);
        $pageTwo = $env->posts()->loadPage(2);
        $pageThree = $env->posts()->loadPage(3);

        $this->assertSame(2, $env->posts()->getMaxPage());
        $this->assertSame(["A", "A", "B"], array_map($getTitle, $pageOne));
        $this->assertSame(["A", "C"], array_map($getTitle, $pageTwo));
        $this->assertEmpty($pageThree);
    }

    public function testInvalidPageAccess()
    {
        $this->expectException(\Exception::class);

        $env = new VirtualEnvironment("/ve");
        $this->populateFileSystem($env);

        $env->posts()->loadPage(-1);
    }
}
