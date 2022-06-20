<?php

namespace lowebf\Test;

require_once("util.php");

use lowebf\Environment;
use lowebf\PhpRuntime;
use lowebf\VirtualEnvironment;
use lowebf\Data\Post;
use lowebf\Error\InvalidFileFormatException;
use lowebf\Persistance\PersistorMarkdown;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    public function testMediaRouting()
    {
        $mediaFilePath = "/ve/data/media/img/abc.png";
        $env = new VirtualEnvironment("/ve");
        $env->saveFile($mediaFilePath, "");

        $runtime = $this->createMock(PhpRuntime::class);

        $runtime->expects($this->once())
            ->method("sendFromFile")
            ->with($this->anything(), $mediaFilePath);

        $runtime->expects($this->once())
            ->method("exit");

        $env->setRuntime($runtime);

        $env->route()->provideAndExit("/media/img/abc.png");
    }

    public function testMediaRoutingPath()
    {
        $env = new VirtualEnvironment("/ve");

        $this->assertSame("/ve/data/media/img/abc.png", $env->route()->pathFor("/media/img/abc.png"));
        $this->assertSame("/ve/data/media/img/abc.png", $env->route()->pathFor("media/img/abc.png"));
    }

    public function testCacheRoutingPath()
    {
        $env = new VirtualEnvironment("/ve");

        $this->assertSame("/ve/cache/thumbs/abc.png", $env->route()->pathFor("/cache/thumbs/abc.png"));
    }

    public function testMediaRoutingUrl()
    {
        $env = new VirtualEnvironment("/ve");

        $this->assertSame("/route.php?x=/media/img/abc.png", $env->route()->urlFor("/media/img/abc.png"));
        $this->assertSame("/route.php?x=/media/img/abc.png", $env->route()->urlFor("media/img/abc.png"));

        $this->assertSame("https://localhost/route.php?x=/media/img/abc.png", $env->route()->absoluteUrlFor("/media/img/abc.png"));
        $this->assertSame("https://localhost/route.php?x=/media/img/abc.png", $env->route()->absoluteUrlFor("media/img/abc.png"));
    }

    public function testChangingScriptPath()
    {
        $env = new VirtualEnvironment("/ve");
        $env->config()->lowebf()->setRoutePath("/nested/relay.php");
        $env->config()->save();

        $this->assertSame("/nested/relay.php?x=/media/img/abc.png", $env->route()->urlFor("/media/img/abc.png"));
        $this->assertSame("/nested/relay.php?x=/media/img/abc.png", $env->route()->urlFor("media/img/abc.png"));

        $this->assertSame("https://localhost/nested/relay.php?x=/media/img/abc.png", $env->route()->absoluteUrlFor("/media/img/abc.png"));
        $this->assertSame("https://localhost/nested/relay.php?x=/media/img/abc.png", $env->route()->absoluteUrlFor("media/img/abc.png"));
    }

    public function testSiteContent()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/site/css/main.css", "");
        $env->saveFile("/ve/site/img/favicon.ico", "");
        $env->saveFile("/ve/site/js/mobile.js", "");

        $this->assertSame("/ve/site/css/main.css", $env->route()->pathFor("/css/main.css"));
        $this->assertSame("/ve/site/img/favicon.ico", $env->route()->pathFor("/img/favicon.ico"));
        $this->assertSame("/ve/site/js/mobile.js", $env->route()->pathFor("/js/mobile.js"));

        $this->assertSame("/route.php?x=/css/main.css", $env->route()->urlFor("/css/main.css"));
        $this->assertSame("/route.php?x=/img/favicon.ico", $env->route()->urlFor("/img/favicon.ico"));
        $this->assertSame("/route.php?x=/js/mobile.js", $env->route()->urlFor("/js/mobile.js"));
    }

    public function testProvideFileNotFound()
    {
        $env = new VirtualEnvironment("/ve");
        $phpRuntime = $env->runtime();

        $runtime = $this->getMockBuilder(get_class($phpRuntime))
            ->setMethods(["setResponseCode"])
            ->getMock();

        $runtime->expects($this->once())
            ->method("setResponseCode")
            ->with(404);

        $env->setRuntime($runtime);

        $env->route()->provideAndExit("/css/main.css");
    }

    public function testGalleryRouting()
    {
        $env = new VirtualEnvironment();

        $this->assertSame("/route.php?x=/galleries/2021-01-01-a/abc.png", $env->route()->urlFor("galleries/2021-01-01-a/abc.png"));
        $this->assertSame(
            "https://localhost/route.php?x=/galleries/2021-01-01-a/abc.png",
            $env->route()->absoluteUrlFor("galleries/2021-01-01-a/abc.png")
        );
    }

    public function testSetDispositionFilename()
    {
        $env = new VirtualEnvironment();
        $env->saveFile("/ve/data/media/img/filename-of-img.png", "");
        $runtime = $this->createMock(PhpRuntime::class);

        $runtime->expects($this->once())
            ->method("setHeader")
            ->with("Content-Disposition", "inline; filename=\"filename-of-img.png\"");

        $env->setRuntime($runtime);

        $env->route()->provideAndExit("/media/img/filename-of-img.png");
    }
}
