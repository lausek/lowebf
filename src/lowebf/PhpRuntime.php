<?php

namespace lowebf;

class PhpRuntime {
    public function exit(?int $statusCode = null) {
        if($statusCode !== null) {
            $this->setResponseCode($statusCode);
        }

        exit;
    }

    public function raiseFileNotFoundAndExit() {
        $this->setResponseCode(404);
        $this->exit();
    }

    public function raiseForbiddenErrorAndExit() {
        $this->setResponseCode(403);
        $this->exit();
    }

    public function raiseInternalErrorAndExit() {
        $this->setResponseCode(500);
        $this->exit();
    }

	public function setResponseCode(int $statusCode) {}

	public function setHeader(string $name, string $value) {}

	public function sendBody(resource $body) {}
}
