<?php

namespace lowebf\Test;

require_once("util.php");

use lowebf\Environment;
use lowebf\Error\FileNotFoundException;
use lowebf\PhpRuntime;
use lowebf\VirtualEnvironment;
use PHPUnit\Framework\TestCase;

final class FilesystemTest extends TestCase
{
    public function testListDirectorySimple()
    {
        $env = new VirtualEnvironment("/ve");
        $env->makeAllDirectories("/ve/cache/");
        $env->saveFile("/ve/cache/thumb.png", "");

        $files = $env->filesystem()->listDirectory("/ve/cache")->unwrap();
        $this->assertSame(1, count($files));
    }

    public function testListDirectorySimpleWithSubdir()
    {
        $env = new VirtualEnvironment("/ve");
        $env->makeAllDirectories("/ve/cache/thumbs/");
        $env->saveFile("/ve/cache/thumbs/a.png", "");
        $env->saveFile("/ve/cache/thumbs/b.png", "");

        $files = $env->filesystem()->listDirectory("/ve/cache")->unwrap();
        $this->assertSame(3, count($files));
        $this->assertSame([], $files["thumbs"]);
    }

    public function testListingUnknownPath()
    {
        $this->expectException(FileNotFoundException::class);
        $env = new VirtualEnvironment("/ve");

        $env->filesystem()->listDirectory("/ve/what")->unwrap();
    }

    public function testVirtualMakeDirectory()
    {
        $env = new VirtualEnvironment("/ve");
        $env->makeAllDirectories("/ve/cache/rest/");

        $this->assertTrue($env->hasFile("/ve/cache"));
        $this->assertTrue($env->hasFile("/ve/cache/rest"));
    }

    public function testListDirectoryRecursive()
    {
        $env = new VirtualEnvironment("/ve");
        $env->makeAllDirectories("/ve/cache/rest/");
        $env->saveFile("/ve/cache/a.png", "");
        $env->saveFile("/ve/cache/rest/b.png", "");

        $files = $env->filesystem()->listDirectoryRecursive("/ve/cache")->unwrap();
        $this->assertTrue(isset($files["rest"]["b.png"]));
    }

    public function testListDirectoryRecursiveDepth()
    {
        $env = new VirtualEnvironment("/ve/");
        $env->saveFile("/ve/lsRecCache/a", "");
        $env->saveFile("/ve/lsRecCache/b/c", "");
        $env->saveFile("/ve/lsRecCache/d/e/f", "");
        $files = $env->filesystem()->listDirectoryRecursive("/ve/lsRecCache", 2)->unwrap();

        $this->assertArrayHasKey("a", $files);
        $this->assertArrayHasKey("b", $files);
        $this->assertArrayHasKey("c", $files["b"]);
        $this->assertArrayHasKey("e", $files["d"]);
        $this->assertSame(1, count($files["d"]));
    }
}
