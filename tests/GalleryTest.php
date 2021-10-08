<?php

namespace lowebf\Test;

require_once("util.php");

use lowebf\VirtualEnvironment;
use PHPUnit\Framework\TestCase;

final class GalleryTest extends TestCase
{
    public function testSaving()
    {
        $env = new VirtualEnvironment();

        $this->assertFalse($env->hasFile("/ve/data/galleries/2021-01-01-a/"));
        $env->galleries()->loadOrCreate("2021-01-01-a")->save();
        $this->assertTrue($env->hasFile("/ve/data/galleries/2021-01-01-a/"));

        $gallery = $env->galleries()->load("2021-01-01-a")->unwrap();

        $this->assertSame("A", $gallery->getTitle());
        $this->assertSame("2021-01-01", $gallery->getDate()->format("Y-m-d"));
    }
}
