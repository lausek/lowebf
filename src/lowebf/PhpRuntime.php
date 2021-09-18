<?php

namespace lowebf;

class PhpRuntime
{
    protected function exitRuntime()
    {
        exit;
    }

    public function exit(?int $statusCode = null) {
        if ($statusCode !== null) {
            $this->setResponseCode($statusCode);
        }

        $this->exitRuntime();
    }

    public function raiseFileNotFoundAndExit()
    {
        $this->exit(404);
    }

    public function raiseForbiddenErrorAndExit()
    {
        $this->exit(403);
    }

    public function raiseInternalErrorAndExit()
    {
        $this->exit(500);
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

            case "jpeg":
                // fallthrough
            case "jpg":
                return "image/jpeg";

            case "png":
                return "image/png";

            case "svg":
                return "image/svg+xml";

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

    public function clearOutputBuffer()
    {
        ob_clean();
    }
}
