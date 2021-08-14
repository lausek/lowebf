<?php

namespace lowebf\Module;

use lowebf\Data\ContentUnit;

class ContentModule extends Module
{
    public function load(string $contentUnitId) : ContentUnit
    {
        $path = $this->env->asAbsoluteDataPath($contentUnitId);
        return ContentUnit::loadFromFile($this->env, $path);
    }

    public function loadOrCreate(string $contentUnitId) : ContentUnit
    {
        $path = $this->env->asAbsoluteDataPath($contentUnitId);
        return ContentUnit::loadFromFileOrCreate($this->env, $path);
    }

    public function delete(string $contentUnitId) {}
}
