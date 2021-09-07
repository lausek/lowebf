<?php

namespace lowebf\Test;

use lowebf\Error\ConversionException;
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

    public function testUnwrapOr()
    {
        $this->assertSame(33, Result::ok(33)->unwrapOr(44));
        $this->assertSame(44, Result::error(new \Exception())->unwrapOr(44));
    }

    public function testMapOk()
    {
        $this->assertSame(
            44,
            Result::ok(33)->mapOk(
                function ($i) {
                    return $i + 11;
                }
            )
                ->unwrap()
        );

        $this->assertSame(
            44,
            Result::error(new \Exception())->mapOk(
                function ($_) {
                    return 0;
                }
            )
                ->unwrapOr(44)
        );
    }

    public function testMapOkTwice()
    {
        $increment = function ($i) { return $i + 1; };
        $result = Result::ok(1)->mapOk($increment)->mapOk($increment);
        $this->assertSame(3, $result->unwrap());
    }

    public function testMappingToBool()
    {
        $this->assertSame(true, Result::ok("1")->mapToBool()->unwrap());
        $this->assertSame(false, Result::ok("0")->mapToBool()->unwrap());
    }

    public function testMappingToInteger()
    {
        $this->assertSame(1, Result::ok("1")->mapToInteger()->unwrap());
        $this->assertSame(500, Result::ok("true")->mapToInteger()->unwrapOr(500));
    }

    public function testMappingError()
    {
        $this->expectException(ConversionException::class);
        $this->assertSame(500, Result::ok("true")->mapToInteger()->unwrap());
    }

    public function testMappingToString()
    {
        $this->assertSame("1.2", Result::ok(1.2)->mapToString()->unwrap());
    }

    public function testUnwrapErrorValue()
    {
        $e = new \Exception("example exception");
        $result = Result::error($e);

        $this->assertTrue($result->isError());
        $this->assertSame($e, $result->unwrapError());
    }

    public function testUnwrapErrorValueWhenOk()
    {
        $this->expectException(\Exception::class);
        $result = Result::error(new \Exception("example exception"));
        $result->unwrap();
    }

    public function testUnwrapAndExitWhileDebugging()
    {
        $runtime = $this->createMock(PhpRuntime::class);
        $env = new VirtualEnvironment();
        $env->config()->lowebf()->setDebugEnabled(true);

        $runtime->expects($this->once())
            ->method("writeOutput")
            ->with($this->anything());

        $env->setRuntime($runtime);

        Result::error(new \Exception("an error occurred"))->unwrapOrExit($env);
    }

    public function testUnwrapAndExitNoMessage()
    {
        $runtime = $this->createMock(PhpRuntime::class);
        $env = new VirtualEnvironment();
        $env->config()->lowebf()->setDebugEnabled(false);

        $runtime->expects($this->never())
            ->method("writeOutput")
            ->with($this->anything());

        $env->setRuntime($runtime);

        Result::error(new \Exception("an error occurred"))->unwrapOrExit($env);
    }
}
