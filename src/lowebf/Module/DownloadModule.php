<?php

namespace lowebf\Module;

class DownloadModule extends Module
{
    public function getFiles() : array
    {
        $path = $this->env->asAbsoluteDataPath("downloads");
        return $this->env->listDirectory($path, true);
    }

    public function provideAndExit(string $downloadId)
    {
        $path = $this->env->asAbsoluteDataPath("downloads/$downloadId");

        $size = $this->env->runtime()->sendFromFile($this->env, $path);

        $this->env->runtime()->setContentTypeFromFile($path);
        $this->env->runtime()->exit();
    }
}
