<?php

namespace lowebf\Test;

require_once("util.php");

use lowebf\Environment;
use lowebf\PhpRuntime;
use lowebf\VirtualEnvironment;
use lowebf\Data\Post;
use lowebf\Persistance\IPersistance;
use PHPUnit\Framework\TestCase;

final class DownloadTest extends TestCase
{
    public function testSaving()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/download/a.pdf", "");
        $env->saveFile("/ve/data/download/b.html", "");
        $env->saveFile("/ve/data/download/deep/c.json", "");

        $downloadFiles = $env->download()->getFiles();

        $this->assertArrayHasKey("a.pdf", $downloadFiles);
        $this->assertArrayHasKey("b.html", $downloadFiles);
        $this->assertArrayHasKey("c.json", $downloadFiles["deep"]);
        $this->assertSame(3, count($downloadFiles));
    }

    public function testListingFilesWithoutDataDirectory()
    {
        $env = new VirtualEnvironment("/ve");
        $this->assertSame([], $env->download()->getFiles());
    }

    public function testProvidingFile()
    {
        $path = "/tmp/data/download/a.json";
        $env = new VirtualEnvironment("/tmp");
        $env->saveFile($path, "{}");

        $runtime = $this->createMock(PhpRuntime::class);

        $runtime->expects($this->once())
            ->method("sendFromFile")
            ->with($this->anything(), $path, true);

        $runtime->expects($this->once())
            ->method("setHeader")
            ->with($this->equalTo("Content-Disposition"), $this->equalTo("attachment; filename=\"a.json\""));

        $runtime->expects($this->once())
            ->method("exit");

        $env->setRuntime($runtime);
        $env->download()->provideAndExit("a.json");
    }
}
