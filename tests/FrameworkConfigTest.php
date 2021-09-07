<?php

namespace lowebf\Test;

use lowebf\Environment;
use lowebf\Error\FileNotFoundException;
use lowebf\PhpRuntime;
use lowebf\VirtualEnvironment;
use PHPUnit\Framework\TestCase;

final class FrameworkConfigTest extends TestCase
{
    public function testIsDebugEnabledDefault()
    {
        $env = new VirtualEnvironment();
        $this->assertFalse($env->config()->lowebf()->isDebugEnabled());
    }

    public function testEnableDebugByFile()
    {
        $env = new VirtualEnvironment();
        $env->saveFile("/ve/data/config.yaml", "lowebf:\n\tdebugEnabled: true");

        $this->assertTrue($env->config()->lowebf()->isDebugEnabled());
    }
}
