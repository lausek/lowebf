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
        $postFilePath = "/ve/data/posts/2021-01-02-ab-c-d.md";

        $env = new VirtualEnvironment("/ve");

        $env->posts()->loadOrCreate("2021-01-02-ab-c-d");

        $this->assertTrue($env->hasFile($postFilePath));
    }

    public function testSetAttributesFromPath() {
        $postFilePath = "/ve/data/posts/2021-01-02-ab-c-d.md";

        $env = new VirtualEnvironment("/ve");

        $env->posts()->loadOrCreate("2021-01-02-ab-c-d");

        $post = $env->posts()->loadOrCreate("2021-01-02-ab-c-d");

        $this->assertSame("2021-01-02", $post->getDate()->format("Y-m-d"));
        $this->assertSame("Ab C D", $post->getTitle());
        $this->assertSame(null, $post->getAuthor());
        $this->assertTrue($env->hasFile($postFilePath));
    }

    public function testSavingContent() {
        $postFilePath = "/ve/data/posts/2021-01-02-ab-c-d.md";

        $env = new VirtualEnvironment("/ve");

        $env->posts()->loadOrCreate("2021-01-02-ab-c-d")
                    ->setAuthor("root")
                    ->setContent("TestContent")
                    ->save();

        $post = $env->posts()->loadOrCreate("2021-01-02-ab-c-d");

        $this->assertSame("2021-01-02", $post->getDate()->format("Y-m-d"));
        $this->assertSame("Ab C D", $post->getTitle());
        $this->assertSame("root", $post->getAuthor());
        $this->assertSame("TestContent", $post->getContent());
        $this->assertTrue($env->hasFile($postFilePath));
    }
}
