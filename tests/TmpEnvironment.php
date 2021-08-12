<?php

namespace lowebf\Test;

use lowebf\Environment;


function dummy(string $path, string $content = null) {
    // only allow touch in /tmp
    assert(strpos($path, "/tmp") === 0);
    // ignore errors if file exists already
    @mkdir($path, 0755, true);
}


class TmpEnvironment extends Environment {
    public function __construct() {
        $rootPath = "/tmp";

        parent::__construct($rootPath);
    }
}
