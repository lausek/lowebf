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

    public function testMediaRoutingUrl()
    {
        $env = new VirtualEnvironment("/ve");

        $this->assertSame("/route.php?x=/media/img/abc.png", $env->route()->urlFor("/media/img/abc.png"));
        $this->assertSame("/route.php?x=/media/img/abc.png", $env->route()->urlFor("media/img/abc.png"));
        $this->assertSame("https://localhost/route.php?x=/media/img/abc.png", $env->route()->absoluteUrlFor("/media/img/abc.png"));
        $this->assertSame("https://localhost/route.php?x=/media/img/abc.png", $env->route()->absoluteUrlFor("media/img/abc.png"));
    }

    public function testChaningScriptPath()
    {
        $env = new VirtualEnvironment("/ve");
        $env->config()->set("routeScriptPath", "/nested/relay.php");

        $this->assertSame("/nested/relay.php?x=/media/img/abc.png", $env->route()->urlFor("/media/img/abc.png"));
        $this->assertSame("/nested/relay.php?x=/media/img/abc.png", $env->route()->urlFor("media/img/abc.png"));
        $this->assertSame("https://localhost/nested/relay.php?x=/media/img/abc.png", $env->route()->absoluteUrlFor("/media/img/abc.png"));
        $this->assertSame("https://localhost/nested/relay.php?x=/media/img/abc.png", $env->route()->absoluteUrlFor("media/img/abc.png"));
    }
}
