<?php

namespace lowebf\Test;

require_once("util.php");

use lowebf\Environment;
use lowebf\VirtualEnvironment;
use lowebf\Data\Post;
use lowebf\Persistance\IPersistance;
use PHPUnit\Framework\TestCase;

final class DownloadTest extends TestCase {
    public function testSaving() {
        $env = new VirtualEnvironment("/ve");
        $env->saveFile("/ve/data/downloads/a.pdf", "");
        $env->saveFile("/ve/data/downloads/b.html", "");
        $env->saveFile("/ve/data/downloads/deep/c.json", "");

        $downloadFiles = $env->download()->getFiles();

        $this->assertArrayHasKey("a.pdf", $downloadFiles);
        $this->assertArrayHasKey("b.html", $downloadFiles);
        $this->assertArrayHasKey("deep/c.json", $downloadFiles);
    }
}
