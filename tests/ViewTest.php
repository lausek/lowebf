<?php

namespace lowebf\Test;

require_once("util.php");

use lowebf\Environment;
use lowebf\PhpRuntime;
use lowebf\VirtualEnvironment;
use lowebf\Data\Post;
use lowebf\Persistance\IPersistance;
use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
    public function testRendering()
    {
        $rawTemplate = "<html>Hello {{ data.name }}</html>";
        $renderedTemplate = "<html>Hello World</html>";
        $data = ["name" => "World"];

        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/site/template/abc.html", $rawTemplate);
        $env->config()->set("cacheEnabled", false);

        $runtime = $this->createMock(PhpRuntime::class);

        $runtime->expects($this->once())
            ->method("writeOutput")
            ->with($renderedTemplate);

        $runtime->expects($this->once())
            ->method("exit");

        $env->setRuntime($runtime);

        $this->assertSame($renderedTemplate, $env->view()->renderToString("abc.html", $data));
        $env->view()->render("abc.html", $data);
    }
}
