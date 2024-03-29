<?php

namespace lowebf\Test;

require_once("util.php");

use lowebf\Environment;
use lowebf\VirtualEnvironment;
use lowebf\Data\Post;
use lowebf\Persistance\IPersistance;
use PHPUnit\Framework\TestCase;

final class ContentTest extends TestCase
{
    public function testSaving()
    {
        $env = new VirtualEnvironment("/ve");

        $contentOne = $env->content()->loadOrCreate("a.yaml");
        $contentTwo = $env->content()->loadOrCreate("b.json");
        $contentThree = $env->content()->loadOrCreate("c.markdown");

        $contentOne->save();
        $contentTwo->save();
        $contentThree->save();

        $this->assertTrue($env->hasFile("/ve/data/content/a.yaml"));
        $this->assertTrue($env->hasFile("/ve/data/content/b.json"));
        $this->assertTrue($env->hasFile("/ve/data/content/c.markdown"));
    }

    public function testLoading()
    {
        $contentJson = '{"team": "B", "names": ["Alfred", "Bernd"]}';
        $contentYaml = "section: Imprint\nage: 15\nhtml:\n    link: abc.de";

        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/content/a.json", $contentJson);
        $env->saveFile("/ve/data/content/b.yaml", $contentYaml);

        $contentUnitJson = $env->content()->loadOrCreate("a.json");
        $contentUnitYaml = $env->content()->loadOrCreate("b.yaml");

        $this->assertSame("B", $contentUnitJson->get("team"));
        $this->assertSame(["Alfred", "Bernd"], $contentUnitJson->get("names"));

        $this->assertSame("Imprint", $contentUnitYaml->get("section"));
        $this->assertSame(15, $contentUnitYaml->get("age"));
        $this->assertSame("abc.de", $contentUnitYaml->get("html")["link"]);
    }

    public function testGettingDefaultValues()
    {
        $contentJson = '{"team": "B", "names": ["Alfred", "Bernd"]}';

        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/content/a.json", $contentJson);

        $contentUnitJson = $env->content()->loadOrCreate("a.json");

        $this->assertSame(null, $contentUnitJson->get("trainer"));
        $this->assertSame("Alfred", $contentUnitJson->get("trainer", "Alfred"));
    }

    public function testAsArray()
    {
        $contentJson = '{"team": "B"}';

        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/content/a.json", $contentJson);

        $contentUnitJson = $env->content()->loadOrCreate("a.json");
        $contentUnitJson->asArray()["team"] = "A";
        $contentUnitJson->save();

        $contentUnitJson = $env->content()->loadOrCreate("a.json");

        $this->assertSame("A", $contentUnitJson->get("team"));
    }

    public function testContentUnitMarkdown()
    {
        $env = new VirtualEnvironment();
        $env->saveFile("/ve/data/content/imprint.md", "# lowebf");
        $env->config()->lowebf()->setCacheEnabled(false);

        $contentUnit = $env->content()->load("imprint.md")->unwrap();

        $this->assertSame("# lowebf", $contentUnit->getContentRaw());
        $this->assertSame("<h1>lowebf</h1>", $contentUnit->getContent());
    }

    public function testAbsoluteUrls()
    {
        $env = new VirtualEnvironment();
        $env->saveFile("/ve/data/content/index.md", "[home](/home)");
        $env->config()->lowebf()->setCacheEnabled(false);

        $contentUnit = $env->content()->load("index.md")->unwrap();

        $this->assertSame("<p><a href=\"/home\">home</a></p>", $contentUnit->getContent());
        $this->assertSame("<p><a href=\"https://localhost/home\">home</a></p>", $contentUnit->getContent(true));
    }

    public function testAbsoluteUrlsForMedia()
    {
        $env = new VirtualEnvironment();
        $env->saveFile("/ve/data/content/index.md", "![icon](/media/img/icon.png)");
        $env->config()->lowebf()->setCacheEnabled(false);

        $contentUnit = $env->content()->load("index.md")->unwrap();

        $this->assertSame("<p><img src=\"/route.php?x=/media/img/icon.png\" alt=\"icon\" /></p>", $contentUnit->getContent());
        $this->assertSame(
            "<p><img src=\"https://localhost/route.php?x=/media/img/icon.png\" alt=\"icon\" /></p>",
            $contentUnit->getContent(true)
        );
    }
}
