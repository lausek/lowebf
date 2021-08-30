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

        $files = $env->filesystem()->listDirectory("/ve/cache");
        $this->assertSame(1, count($files));
    }

    public function testListDirectorySimpleWithSubdir()
    {
        $env = new VirtualEnvironment("/ve");
        $env->makeAllDirectories("/ve/cache/thumbs/");
        $env->saveFile("/ve/cache/thumbs/a.png", "");
        $env->saveFile("/ve/cache/thumbs/b.png", "");

        $files = $env->filesystem()->listDirectory("/ve/cache");
        $this->assertSame(3, count($files));
        $this->assertSame([], $files["thumbs/"]);
    }

    public function testListingUnknownPath()
    {
        $this->expectException(FileNotFoundException::class);
        $env = new VirtualEnvironment("/ve");

        $files = $env->filesystem()->listDirectory("/ve/what");
    }

    public function testVirtualMakeDirectory()
    {
        $env = new VirtualEnvironment("/ve");
        $env->makeAllDirectories("/ve/cache/rest/");

        $this->assertTrue($env->hasFile("/ve/cache"));
        $this->assertTrue($env->hasFile("/ve/cache/rest"));
    }
}
