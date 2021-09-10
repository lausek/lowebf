<?php

namespace lowebf\Module;

use lowebf\Error\FileNotFoundException;

class DownloadModule extends Module
{
    public function getFiles() : array
    {
        $path = $this->env->asAbsoluteDataPath("download");

        return $this->env->listDirectoryRecursive($path)->unwrapOr([]);
    }

    public function provideAndExit(string $downloadId)
    {
        $path = $this->env->asAbsoluteDataPath("download/$downloadId");
        $filename = pathinfo($path, PATHINFO_BASENAME);

        if (!$this->env->hasFile($path)) {
            $this->env->runtime()->raiseFileNotFoundAndExit();

            return;
        }

        $this->env->runtime()->setContentTypeFromFile($path);
        $this->env->runtime()->setHeader("Content-Disposition", "attachment; filename=\"$filename\"");
        $this->env->runtime()->sendFromFile($this->env, $path);
        $this->env->runtime()->exit();
    }
}
