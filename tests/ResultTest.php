<?php

namespace lowebf\Test;

use lowebf\Error\InvalidFileFormatException;
use lowebf\PhpRuntime;
use lowebf\Result;
use lowebf\VirtualEnvironment;
use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase
{
    public function testUnwrappingOk()
    {
        $result = Result::ok(2);
        $this->assertTrue($result->isOk());
        $this->assertFalse($result->isError());
        $this->assertSame(2, $result->unwrap());
    }

    public function testUnwrappingError()
    {
        $this->expectException(\Exception::class);

        $result = Result::error(new \Exception());
        $this->assertFalse($result->isOk());
        $this->assertTrue($result->isError());
        $result->unwrap();
    }

    public function testUnwrappingErrorAndExit()
    {
        $env = new VirtualEnvironment();

        $runtime = $this->createMock(PhpRuntime::class);

        $runtime->expects($this->once())
            ->method("exit")
            ->with(500);

        $env->setRuntime($runtime);

        $result = Result::error(new \Exception());

        $result->unwrapOrExit($env);
    }

    public function testUnwrappingErrorAndExitCustomCode()
    {
        $exitWithCode = 404;
        $env = new VirtualEnvironment();

        $runtime = $this->createMock(PhpRuntime::class);

        $runtime->expects($this->once())
            ->method("exit")
            ->with($exitWithCode);

        $env->setRuntime($runtime);

        $result = Result::error(new \Exception());

        $result->unwrapOrExit($env, $exitWithCode);
    }
}
