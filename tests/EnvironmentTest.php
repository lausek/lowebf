<?php

namespace lowebf\Test;

require_once("util.php");

use lowebf\Environment;
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
        $this->assertArrayHasKey("deep/c", $filesDeep);
    }
}
