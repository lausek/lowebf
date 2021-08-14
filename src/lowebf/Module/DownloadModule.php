<?php

namespace lowebf\Module;

class DownloadModule extends Module
{
    public function getFiles() : array {
        $path = $this->env->asAbsoluteDataPath("downloads");
        return $this->env->listDirectory($path, true);
    }

    public function provideAndExit(string $downloadId) {
        $path = $this->env->asAbsoluteDataPath("downloads/$downloadId");
        $mimeType = mime_content_type($path);

        $size = $this->env->runtime()->sendFromFile($path);

        $this->env->runtime()->setHeader("Content-Type", $mimeType);
        $this->env->runtime()->exit();
    }
}
