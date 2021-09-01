<?php

namespace lowebf\Test;

use lowebf\Filesystem\VirtualFilesystem;
use lowebf\VirtualEnvironment;
use PHPUnit\Framework\TestCase;

function getTestFileContent() : string
{
    $testDirectoryPath = dirname(__FILE__);
    return file_get_contents("$testDirectoryPath/img/emblem.jpeg");
}

final class ThumbnailTest extends TestCase
{
    public function testCachePathDetermination()
    {
        $env = new VirtualEnvironment();
        $dataPath = "/media/img/a.png";
        $staticPath = "/site/img/b.png";

        $this->assertSame("/ve/cache/thumb/media/img/a.png", $env->thumbnail()->cachePathFor($dataPath));
        $this->assertSame("/ve/cache/thumb/site/img/b.png", $env->thumbnail()->cachePathFor($staticPath));
    }

    public function testThumbnailGeneration()
    {
        $env = new VirtualEnvironment();
        $env->saveFile("/ve/data/media/img/a.jpeg", getTestFileContent());

        $this->assertSame("/ve/cache/thumb/media/img/a.jpeg", $env->thumbnail()->pathFor("/media/img/a.jpeg"));
        $this->assertTrue($env->cache()->exists("/thumb/media/img/a.jpeg"));
    }

    public function testAvoidGenerationIfCached()
    {
        $env = new VirtualEnvironment();

        $filesystem = $this->getMockBuilder(VirtualFilesystem::class)
            ->setConstructorArgs([$env])
            ->setMethods(["loadFile", "saveFile"])
            ->getMock();

        $filesystem->expects($this->once())
            ->method("saveFile")
            ->with("/ve/cache/thumb/media/img/a.jpeg");

        $filesystem->expects($this->once())
            ->method("loadFile")
            ->with("/ve/data/media/img/a.jpeg")
            ->will($this->returnValue(getTestFileContent()));

        $env->setFilesystem($filesystem);

        $this->assertSame("/ve/cache/thumb/media/img/a.jpeg", $env->thumbnail()->pathFor("/media/img/a.jpeg"));
        $this->assertSame("/ve/cache/thumb/media/img/a.jpeg", $env->thumbnail()->pathFor("/media/img/a.jpeg"));
    }
}
