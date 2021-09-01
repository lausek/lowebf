<?php

namespace lowebf\Module;

use lowebf\Error\FileNotFoundException;

class DownloadModule extends Module
{
    public function getFiles() : array
    {
        $path = $this->env->asAbsoluteDataPath("download");

        try {
            return $this->env->listDirectoryRecursive($path);
        } catch (FileNotFoundException $e) {
            return [];
        }
    }

    public function provideAndExit(string $downloadId)
    {
        $path = $this->env->asAbsoluteDataPath("download/$downloadId");
        $filename = pathinfo($path, PATHINFO_BASENAME);

        $this->env->runtime()->setContentTypeFromFile($path);
        $this->env->runtime()->setHeader("Content-Disposition", "attachment; filename=\"$filename\"");
        $this->env->runtime()->sendFromFile($this->env, $path);
        $this->env->runtime()->exit();
    }
}
