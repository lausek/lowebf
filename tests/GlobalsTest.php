<?php

namespace lowebf\Test;

use lowebf\VirtualEnvironment;
use PHPUnit\Framework\TestCase;

final class GlobalsTest extends TestCase
{
    public function testGettingSuperglobalsGet()
    {
        $env = new VirtualEnvironment();
        $env->setGetGlobal("pageId", "1");
        $env->setGetGlobal("randomString", "abl");

        $this->assertSame(1, $env->globals()->get("pageId")->mapToInteger()->unwrap());
        $this->assertSame("abl", $env->globals()->get("randomString")->unwrap());
        $this->assertSame("default", $env->globals()->get("notKnown")->unwrapOr("default"));
    }

    public function testGettingSuperglobalsPost()
    {
        $env = new VirtualEnvironment();
        $env->setPostGlobal("userid", "1");
        $env->setPostGlobal("displayDebug", "true");

        $this->assertSame(1, $env->globals()->post("userid")->mapToInteger()->unwrap());
        $this->assertSame(true, $env->globals()->post("displayDebug")->mapToBool()->unwrap());
        $this->assertSame("default", $env->globals()->get("notKnown")->unwrapOr("default"));
    }

    public function testGettingSuperglobalsServer()
    {
        $env = new VirtualEnvironment();
        $env->setServerGlobal("DOCUMENT_ROOT", "/ve");

        $this->assertSame("/ve", $env->globals()->server("DOCUMENT_ROOT")->unwrap());
    }
}
