<?php

namespace lowebf\Test;

require_once("util.php");

use lowebf\Environment;
use lowebf\VirtualEnvironment;
use lowebf\Data\Post;
use lowebf\Persistance\IPersistance;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testSaving()
    {
        $env = new VirtualEnvironment("/ve");

        $config = $env->config();
        $config->set("debug", "false");
        $config->set("homepage", "www.example.de");
        $config->save();

        $this->assertTrue($env->hasFile("/ve/data/config.yaml"));
    }

    public function testLoading()
    {
        $fileContent = "debug: false\nhomepage: 'www.example.de'\n";

        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/config.yaml", $fileContent);
        $config = $env->config();

        $this->assertSame(false, $config->get("debug"));
        $this->assertSame("www.example.de", $config->get("homepage"));
    }
}