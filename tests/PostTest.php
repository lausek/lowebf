<?php

namespace lowebf\Test;

require_once("TmpEnvironment.php");

use lowebf\Data\Post;
use lowebf\Persistance\IPersistance;
use PHPUnit\Framework\TestCase;

final class PostTest extends TestCase {
    public function testSavingPost() {
    }

    public function testSetAttributesFromPath() {
        dummy("/tmp/data/posts/2021-01-02-a-b-c-d.md");

        $env = new TmpEnvironment();
        $post = $env->posts()->loadOrCreate("2021-01-02-a-b-c-d");

        $this->assertSame("2021-01-02", $post->getDate()->format("Y-m-d"));
        $this->assertSame("A B C D", $post->getTitle());
    }
}
