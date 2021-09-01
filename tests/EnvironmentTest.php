<?php

namespace lowebf\Test;

require_once("util.php");

use lowebf\Environment;
use lowebf\VirtualEnvironment;
use lowebf\Error\FileNotFoundException;
use PHPUnit\Framework\TestCase;

final class EnvironmentTest extends TestCase
{
    public function testPathCreation()
    {
        dummy("/tmp/data/");

        $env = new Environment("/tmp/");
        $this->assertSame("/tmp", $env->getRootPath());
        $this->assertSame("/tmp/data", $env->getDataPath());

        $env = new Environment("/tmp/", "/tmp");
        $this->assertSame("/tmp", $env->getRootPath(), "/tmp");
        $this->assertSame("/tmp", $env->getDataPath(), "/tmp");
    }

    public function testAbsolutePath()
    {
        dummy("/tmp/index.php");

        $env = new Environment("/tmp/");
        $this->assertSame("/tmp/index.php", $env->asAbsolutePath("/index.php"));
        $this->assertSame("/tmp/index.php", $env->asAbsolutePath("index.php"));
    }

    public function testAbsoluteDataPath()
    {
        dummy("/tmp/data/index.php");
        dummy("/tmp/deep/index.php");

        $env = new Environment("/tmp/");
        $this->assertSame("/tmp/data/index.php", $env->asAbsoluteDataPath("/index.php"));
        $this->assertSame("/tmp/data/index.php", $env->asAbsoluteDataPath("index.php"));

        $env = new Environment("/tmp/", "/tmp/deep");
        $this->assertSame("/tmp/deep/index.php", $env->asAbsoluteDataPath("/index.php"));
        $this->assertSame("/tmp/deep/index.php", $env->asAbsoluteDataPath("index.php"));
    }

    public function testCachePath()
    {
        dummy("/tmp/cache");

        $env = new Environment("/tmp/");
        $this->assertSame("/tmp/cache", $env->cache()->getPath());
    }

    public function testListingDirectory()
    {
        dummy("/tmp/lsCache/a");
        dummy("/tmp/lsCache/b");
        dummy("/tmp/lsCache/deep/c");

        $env = new Environment("/tmp/");
        $files = $env->listDirectory("/tmp/lsCache");
        $filesDeep = $env->listDirectory("/tmp/lsCache", true);

        $this->assertArrayHasKey("a", $files);
        $this->assertArrayHasKey("b", $files);
        $this->assertArrayHasKey("a", $filesDeep);
        $this->assertArrayHasKey("b", $filesDeep);
        $this->assertArrayHasKey("c", $filesDeep["deep"]);
    }

    public function testListingDirectoryRecursiveDepth()
    {
        dummy("/tmp/lsRecCache/a");
        dummy("/tmp/lsRecCache/b/c");
        dummy("/tmp/lsRecCache/d/e/f");

        $env = new Environment("/tmp/");
        $files = $env->filesystem()->listDirectoryRecursive("/tmp/lsRecCache", 2);

        $this->assertArrayHasKey("a", $files);
        $this->assertArrayHasKey("b", $files);
        $this->assertArrayHasKey("c", $files["b"]);
        $this->assertArrayHasKey("e", $files["d"]);
        $this->assertSame(1, count($files["d"]));
    }

    public function testFindMatchingFile()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/posts/2021-09-01-a.md", "");
        $env->saveFile("/ve/data/posts/2021-09-01-b.json", "");
        $env->saveFile("/ve/data/posts/2021-09-15-c.yaml", "");

        $this->assertSame("/ve/data/posts/2021-09-01-a.md", $env->findWithoutFileExtension("/ve/data/posts", "2021-09-01-a"));
        $this->assertSame("/ve/data/posts/2021-09-01-b.json", $env->findWithoutFileExtension("/ve/data/posts", "2021-09-01-b"));
        $this->assertSame("/ve/data/posts/2021-09-15-c.yaml", $env->findWithoutFileExtension("/ve/data/posts", "2021-09-15-c"));
    }

    public function testFindMatchingFilePreference()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/config.yaml", "");
        $env->saveFile("/ve/data/config.md", "");

        $this->assertSame("/ve/data/config.yaml", $env->findWithoutFileExtension("/ve/data", "config"));
    }

    public function testFindMatchingFileNotFound()
    {
        $env = new VirtualEnvironment("/ve");

        $this->assertNull($env->findWithoutFileExtension("/ve/data", "config"));
    }

    public function testRaiseExceptionOnPathListing()
    {
        $this->expectException(FileNotFoundException::class);
        $env = new VirtualEnvironment("/ve");

        $env->listDirectory("/ve/data");
    }
}
