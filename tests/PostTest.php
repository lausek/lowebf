<?php

namespace lowebf\Test;

require_once("util.php");

use lowebf\Environment;
use lowebf\VirtualEnvironment;
use lowebf\Data\Post;
use lowebf\Persistance\IPersistance;
use PHPUnit\Framework\TestCase;

final class PostTest extends TestCase {
    public function testSavingPost() {
        $dir = tmpdir();
        $postFilePath = "$dir/data/posts/2021-01-02-ab-c-d.md";

        $env = new VirtualEnvironment($dir);

        $env->posts()->loadOrCreate("2021-01-02-ab-c-d");

        $this->assertTrue($env->hasFile($postFilePath));
    }

    public function testSetAttributesFromPath() {
        $dir = tmpdir();
        $postFilePath = "$dir/data/posts/2021-01-02-ab-c-d.md";

        $env = new VirtualEnvironment($dir);

        $post = $env->posts()->loadOrCreate("2021-01-02-ab-c-d");

        $this->assertSame("2021-01-02", $post->getDate()->format("Y-m-d"));
        $this->assertSame("Ab C D", $post->getTitle());
        $this->assertTrue($env->hasFile($postFilePath));
    }
}
