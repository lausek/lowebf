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

    public function testCacheAccess()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/site/css/main.css", "");
        $env->saveFile("/ve/site/template/render-main.html", "{{ stylesheet('main.css') }}");
        $env->config()->set("cacheEnabled", false);

        $this->assertSame(
            "<link rel='stylesheet' type='text/css' href='/route.php?x=/css/main.css'/>",
            $env->view()->renderToString("render-main.html")
        );
    }

    public function testCacheCompileAccess()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/site/css/main.scss", "");
        $env->saveFile("/ve/site/template/scss.html", "{{ stylesheet('main.scss') }}");
        $env->config()->set("cacheEnabled", false);

        $this->assertSame(
            "<link rel='stylesheet' type='text/css' href='/route.php?x=/cache/css/main-0.css'/>",
            $env->view()->renderToString("scss.html")
        );
        $this->assertTrue(isset($env->getFileSystem()["/ve/cache/css/main-0.css"]));
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

    public function testResourceUrls()
    {
        $cssTemplate = "{{ resourceUrl('/css/main.css') }}";
        $imgTemplate = "{{ resourceUrl('/img/favicon.ico') }}";
        $jsTemplate = "{{ resourceUrl('/js/mobile.js') }}";
        $cssAbsoluteTemplate = "{{ resourceAbsoluteUrl('/css/main.css') }}";
        $imgAbsoluteTemplate = "{{ resourceAbsoluteUrl('/img/favicon.ico') }}";
        $jsAbsoluteTemplate = "{{ resourceAbsoluteUrl('/js/mobile.js') }}";

        $env = new VirtualEnvironment("/ve");
        $env->config()->set("cacheEnabled", false);
        $env->saveFile("/ve/site/template/css.html", $cssTemplate);
        $env->saveFile("/ve/site/template/img.html", $imgTemplate);
        $env->saveFile("/ve/site/template/js.html", $jsTemplate);
        $env->saveFile("/ve/site/template/cssAbsolute.html", $cssAbsoluteTemplate);
        $env->saveFile("/ve/site/template/imgAbsolute.html", $imgAbsoluteTemplate);
        $env->saveFile("/ve/site/template/jsAbsolute.html", $jsAbsoluteTemplate);

        $this->assertSame("/route.php?x=/css/main.css", $env->view()->renderToString("css.html"));
        $this->assertSame("/route.php?x=/img/favicon.ico", $env->view()->renderToString("img.html"));
        $this->assertSame("/route.php?x=/js/mobile.js", $env->view()->renderToString("js.html"));

        $this->assertSame("https://localhost/route.php?x=/css/main.css", $env->view()->renderToString("cssAbsolute.html"));
        $this->assertSame("https://localhost/route.php?x=/img/favicon.ico", $env->view()->renderToString("imgAbsolute.html"));
        $this->assertSame("https://localhost/route.php?x=/js/mobile.js", $env->view()->renderToString("jsAbsolute.html"));
    }

    public function testContentAccessInTemplate()
    {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/posts/2021-09-01-a.md", "---\n---\nhereisasecret");
        $env->saveFile("/ve/site/template/post-view.html", "{{ data.date|date('Y-m-d') }} with {{ data.content }}");
        $env->config()->set("cacheEnabled", false);

        $post = $env->posts()->load("2021-09-01-a");
        $this->assertSame("2021-09-01 with hereisasecret", $env->view()->renderToString("post-view.html", $post));
    }
}
