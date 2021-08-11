<?php

namespace lowebf\Module;

abstract class Module {

    /* @var Environment */
	protected $env;

	function __construct(Environment $env) {
        $this->env = $env;
	}
}
