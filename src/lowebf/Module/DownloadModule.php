<?php

namespace lowebf\Module;

class DownloadModule extends Module
{
    public function getFiles() : array {
        $path = $env->asAbsoluteDataPath("downloads");

        foreach($env->listDirectory($path, true)) {}

        return [];
    }

    public function provideAndExit(string $downloadId) {}
}
