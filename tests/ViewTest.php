<?php

/**
 * NOTE: Always use new template names for testing! Twig will generate a class name from the
 *          caching key and import it into scope thus leading to state changes across tests.
 * */

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
        $env->saveFile("/ve/site/template/main.html", $rawTemplate);
        $env->config()->set("cacheEnabled", false);

        $runtime = $this->createMock(PhpRuntime::class);

        $runtime->expects($this->once())
            ->method("writeOutput")
            ->with($renderedTemplate);

        $runtime->expects($this->once())
            ->method("exit");

        $env->setRuntime($runtime);

        $this->assertSame($renderedTemplate, $env->view()->renderToString("main.html", $data));
        $env->view()->render("main.html", $data);
    }

    public function testStylesheetExtension()
    {
        $rawScss = "body {}";
        $rawTemplate = "<html><head>{{ stylesheet('main.scss') }}</head></html>";

        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/site/css/main.scss", $rawScss);
        $env->saveFile("/ve/site/template/index.html", $rawTemplate);
        $env->config()->set("cacheEnabled", false);

        $runtime = $this->createMock(PhpRuntime::class);

        $runtime->expects($this->exactly(2))
            ->method("writeOutput")
            ->withConsecutive(
                [$this->equalTo("<link rel='stylesheet' type='text/css' href=''/>")],
                [$this->equalTo("<html><head></head></html>")]
            );

        $runtime->expects($this->once())
            ->method("exit");

        $env->setRuntime($runtime);

        $env->view()->render("index.html");
    }

    public function testHeadersExtension()
    {
        $postContent = "";
        $rawTemplate = "<html><head>{{ linkPreviewHeaders(data) }}</head></html>";

        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/posts/2021-01-01-a.yaml", $postContent);
        $env->saveFile("/ve/site/template/headers.html", $rawTemplate);
        $env->config()->set("cacheEnabled", false);

        $post = $env->posts()->load("2021-01-01-a");
        $renderedTemplate = $env->view()->renderToString("headers.html", (array)$post);

        $dom = \DOMDocument::loadHTML($renderedTemplate);
        $this->assertNotEmpty($dom->getElementsByTagName("meta"));
    }

    public function testLimitingStringsExtension()
    {
        $rawTemplate = "{{ data.str | limitLength(3) }}";

        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/site/template/limit.html", $rawTemplate);
        $env->config()->set("cacheEnabled", false);

        $this->assertSame("abc", $env->view()->renderToString("limit.html", ["str" => "abc"]));
        $this->assertSame("ab…", $env->view()->renderToString("limit.html", ["str" => "abcd"]));
    }

    public function testUrlExtension()
    {
        $rawTemplate = "{{ url('view.php', {id: 'abc'}) }}";

        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/site/template/url.html", $rawTemplate);
        $env->config()->set("cacheEnabled", false);

        $this->assertSame("/view.php?id=abc", $env->view()->renderToString("url.html"));
    }

    public function testSettingDebugMode()
    {
        $configYaml = "debugEnabled: true\n";

        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/config.yaml", $configYaml);

        $this->assertTrue($env->view()->getTwigEnvironment()->isDebug());
    }

    public function testDebugModeDisabledByDefault()
    {
        $env = new VirtualEnvironment("/ve");

        $this->assertFalse($env->view()->getTwigEnvironment()->isDebug());
    }
}
