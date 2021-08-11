<?php

namespace lowebf;

class PhpRuntime {

	private Environment $environment;

	function __construct() {
	}

	public function exit(?int $statusCode) {}

	public function raiseFileNotFoundAndExit() {}

	public function raiseForbiddenErrorAndExit() {}

	public function raiseInternalErrorAndExit() {}

	public function setResponseCode(int $statusCode) {}

	public function setHeader(string $name, string $value) {}

	public function sendBody(resource $body) {}
}
