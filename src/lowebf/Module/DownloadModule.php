<?php

namespace lowebf\Module;

class DownloadModule extends Module
{
    public function getFiles() : array {
        $path = $this->env->asAbsoluteDataPath("downloads");
        return $this->env->listDirectory($path, true);
    }

    public function provideAndExit(string $downloadId) {}
}
