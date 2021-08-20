<?php

namespace lowebf;

class PhpRuntime
{
    public function exit(?int $statusCode = null) {
        if ($statusCode !== null) {
            $this->setResponseCode($statusCode);
        }

        exit;
    }

    public function raiseFileNotFoundAndExit()
    {
        $this->setResponseCode(404);
        $this->exit();
    }

    public function raiseForbiddenErrorAndExit()
    {
        $this->setResponseCode(403);
        $this->exit();
    }

    public function raiseInternalErrorAndExit()
    {
        $this->setResponseCode(500);
        $this->exit();
    }

    public function getContentType(string $path)
    {
        $fileExtension = pathinfo($path, PATHINFO_EXTENSION);
        $fileExtension = strtolower($fileExtension);

        switch ($fileExtension) {
            case "css":
                return "text/css";

            case "html":
                return "text/html";

            case "js":
                return "text/javascript";

            case "json":
                return "application/json";

            case "xml":
                return "application/xml";

            default:
                return mime_content_type($path);
        }
    }

    public function setContentTypeFromFile(string $path)
    {
        $this->setHeader("Content-Type", $this->getContentType($path));
    }

    public function setResponseCode(int $statusCode)
    {
        http_response_code($statusCode);
    }

    public function setHeader(string $name, string $value)
    {
        header("$name: $value");
    }

    public function sendFromFile(Environment $env, string $path, bool $setContentType = true)
    {
        if ($setContentType === true) {
            $this->setContentTypeFromFile($path);
        }

        $env->sendFile($path);
    }

    public function writeOutput(string $output)
    {
        echo $output;
    }
}
