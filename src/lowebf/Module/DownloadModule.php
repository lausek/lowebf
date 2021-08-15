<?php

namespace lowebf\Module;

class DownloadModule extends Module
{
    public function determineContentType($path)
    {
        $fileExtension = pathinfo($path, PATHINFO_EXTENSION);
        $fileExtension = strtolower($fileExtension);

        switch ($fileExtension) {
            case "json":
                return "application/json";

            case "xml":
                return "application/xml";

            default:
                return mime_content_type($path);
        }
    }

    public function getFiles() : array
    {
        $path = $this->env->asAbsoluteDataPath("downloads");
        return $this->env->listDirectory($path, true);
    }

    public function provideAndExit(string $downloadId)
    {
        $path = $this->env->asAbsoluteDataPath("downloads/$downloadId");
        $mimeType = $this->determineContentType($path);

        $size = $this->env->runtime()->sendFromFile($this->env, $path);

        $this->env->runtime()->setHeader("Content-Type", $mimeType);
        $this->env->runtime()->exit();
    }
}
