<?php

namespace lowebf\Module;

use lowebf\Environment;

abstract class Module {

    /* @var Environment */
	protected $env;

	function __construct(Environment $env) {
        $this->env = $env;
	}
}
