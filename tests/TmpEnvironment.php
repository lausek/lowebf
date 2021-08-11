<?php

namespace lowebf\Test;

use lowebf\Environment

class TmpEnvironment extends Environment {
    public function __construct() {
        $rootPath = "";

        parent::__construct($rootPath);
    }
}
