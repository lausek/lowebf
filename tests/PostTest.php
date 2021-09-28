<?php

namespace lowebf\Test;

require_once("util.php");

use lowebf\Environment;
use lowebf\VirtualEnvironment;
use lowebf\Data\Post;
use lowebf\Error\InvalidFileFormatException;
use lowebf\Persistance\IPersistance;
use lowebf\Result;
use PHPUnit\Framework\TestCase;

final class PostTest extends TestCase
{
    public function testSavingPostOnCreation()
    {
        $postFilePath = "/ve/data/posts/2021-01-02-ab-c-d.md";

        $env = new VirtualEnvironment("/ve");

        $env->posts()->loadOrCreate("2021-01-02-ab-c-d");

        $this->assertTrue($env->hasFile($postFilePath));
    }

    public function testSavingPostExplicit()
    {
        $postFilePath = "/ve/data/posts/2021-01-02-ab-c-d.md";

        $env = new VirtualEnvironment("/ve");

        $env->posts()->loadOrCreate("2021-01-02-ab-c-d")->save();

        $this->assertTrue($env->hasFile($postFilePath));
    }

    public function testSetAttributesFromPath()
    {
        $postFilePath = "/ve/data/posts/2021-01-02-ab-c-d.md";

        $env = new VirtualEnvironment("/ve");
        $env->saveFile($postFilePath, "---\n---\n");

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

        $env->posts()
            ->loadOrCreate("2021-01-02-ab-c-d")
            ->setAuthor("root")
            ->setContent("TestContent **big**")
            ->save();

        $post = $env->posts()->loadOrCreate("2021-01-02-ab-c-d");

        $this->assertSame("2021-01-02", $post->getDate()->format("Y-m-d"));
        $this->assertSame("Ab C D", $post->getTitle());
        $this->assertSame("2021-01-02-ab-c-d", $post->getId());
        $this->assertSame("root", $post->getAuthor());
        $this->assertSame("TestContent **big**", $post->getContentRaw());
        $this->assertSame("<p>TestContent <strong>big</strong></p>", $post->getContent());
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

        $env->expects($this->once())
            ->method("loadFile")
            ->with($this->equalTo("/ve/data/config.yaml"));

        $post = $env->posts()->load("2021-01-02-ab-c-d")->unwrap();
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

        $env->expects($this->exactly(2))
            ->method("loadFile")
            ->withConsecutive(["/ve/data/config.yaml"], [$postFilePath])
            ->willReturnOnConsecutiveCalls(Result::ok(""), Result::ok($postFileContent));

        $post = $env->posts()->load("2021-01-02-ab-c-d")->unwrap();
        $this->assertSame("2021-01-02", $post->getDate()->format("Y-m-d"));
        $this->assertSame("Ab C D", $post->getTitle());
        $this->assertSame("abc", $post->getContentRaw());
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

        $this->assertEmpty($env->filesystem()->asArray());

        foreach ($files as $path => $content) {
            $env->saveFile("/ve/data/posts/$path", $content);
        }

        $this->assertNotEmpty($env->filesystem()->asArray());
    }

    public function testLoadingPage()
    {
        $postDates = [];
        $postTitles = [];

        $env = new VirtualEnvironment("/ve");
        $this->populateFileSystem($env);
        $posts = $env->posts()->loadPage(1)->unwrap();

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

        $pageOne = $env->posts()->loadPage(1)->unwrap();
        $pageTwo = $env->posts()->loadPage(2)->unwrap();
        $pageThree = $env->posts()->loadPage(3)->unwrap();

        $this->assertSame(2, $env->posts()->getMaxPage());
        $this->assertSame(["A", "A", "B"], array_map($getTitle, $pageOne));
        $this->assertSame(["A", "C"], array_map($getTitle, $pageTwo));
        $this->assertEmpty($pageThree);
    }

    public function testPostsPerPageConfiguration()
    {
        $env = new VirtualEnvironment("/ve");
        $this->populateFileSystem($env);

        $env->saveFile("/ve/data/config.yaml", "lowebf:\n\tpostsPerPage: 2");

        $pageOne = $env->posts()->loadPage(1)->unwrap();
        $pageTwo = $env->posts()->loadPage(2)->unwrap();
        $pageThree = $env->posts()->loadPage(3)->unwrap();
        $pageFour = $env->posts()->loadPage(4)->unwrap();

        $this->assertSame(3, $env->posts()->getMaxPage());
        $this->assertSame(2, count($pageOne));
        $this->assertSame(2, count($pageTwo));
        $this->assertSame(2, count($pageThree));
        $this->assertEmpty($pageFour);
    }

    public function testInvalidPageAccess()
    {
        $this->expectException(\Exception::class);

        $env = new VirtualEnvironment("/ve");
        $this->populateFileSystem($env);

        $env->posts()->loadPage(-1)->unwrap();
    }

    public function testDescriptionFull()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/posts/2021-01-01-a.json", '{"content":"description not long enough"}');

        $this->assertSame("description not long enough", $env->posts()->load("2021-01-01-a")->unwrap()->getDescription());
    }

    public function testDescriptionShort()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/posts/2021-01-01-a.json", '{"content":"description not long enough"}');

        $this->assertSame("description not…", $env->posts()->load("2021-01-01-a")->unwrap()->getDescription(16));
    }

    public function testDescriptionLengthConfiguration()
    {
        $env = new VirtualEnvironment();
        $env->saveFile("/ve/data/posts/2021-08-31-lazy-loading.md", "abcdef");
        $env->saveFile("/ve/data/config.yaml", "lowebf:\n\tpostDescriptionLength: 4");

        $post = $env->posts()->load("2021-08-31-lazy-loading")->unwrap();

        $this->assertSame(4, mb_strlen($post->getDescription()));
    }

    public function testUrlMarkdownFilter()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/site/css/main.css", "");
        $env->saveFile("/ve/data/posts/2021-01-01-a.md", "[link to main.css](/css/main.css)");

        $post = $env->posts()->load("2021-01-01-a")->unwrap();
        $this->assertSame("<p><a href=\"/route.php?x=/css/main.css\">link to main.css</a></p>", $post->getContent());
    }

    public function testImageUrlMarkdownFilter()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/media/img/entry.png", "");
        $env->saveFile("/ve/data/posts/2021-01-01-a.md", "![alternative text](/media/img/entry.png)");

        $post = $env->posts()->load("2021-01-01-a")->unwrap();
        $this->assertSame("<p><img src=\"/route.php?x=/media/img/entry.png\" alt=\"alternative text\" /></p>", $post->getContent());
    }

    public function testUrlMarkdownFilterAvoidForeignDomain()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/posts/2021-01-01-a.md", "[link to author](https://lausek.eu)");

        $post = $env->posts()->load("2021-01-01-a")->unwrap();
        $this->assertSame("<p><a href=\"https://lausek.eu\">link to author</a></p>", $post->getContent());
    }

    public function testLazyLoadingOtherAttributes()
    {
        $env = new VirtualEnvironment();
        $env->saveFile("/ve/data/posts/2021-08-31-lazy-loading.md", "---\npreview: <link>\n---\n");

        $post = $env->posts()->load("2021-08-31-lazy-loading")->unwrap();

        $this->assertSame("<link>", $post->preview);
    }

    public function testSkipUnsupportedPostFiles()
    {
        $env = new VirtualEnvironment();
        $env->saveFile("/ve/data/posts/2021-08-31-lazy-loading.md", "");
        $env->saveFile("/ve/data/posts/.gitkeep", "");

        $this->assertSame(1, count($env->posts()->loadPosts()));
    }

    public function testOverwritingTitle()
    {
        $env = new VirtualEnvironment();
        $env->saveFile("/ve/data/posts/2021-08-31-lazy-loading.md", "---\ntitle: Something different\n---\n");

        $post = $env->posts()->load("2021-08-31-lazy-loading")->unwrap();

        // title is equal to file name because file content was not loaded
        $this->assertSame("Lazy Loading", $post->getTitle());

        $post->getContent();

        $this->assertSame("Something different", $post->getTitle());
    }

    public function testOverwritingTitleExplicit()
    {
        $env = new VirtualEnvironment();
        $env->saveFile("/ve/data/posts/2021-08-31-lazy-loading.md", "---\ntitle: Something different\n---\n");

        $post = $env->posts()->load("2021-08-31-lazy-loading")->unwrap();

        // title is equal to file name because file content was not loaded
        $this->assertSame("Lazy Loading", $post->getTitle());

        $post->triggerLoading();

        $this->assertSame("Something different", $post->getTitle());
    }
}
