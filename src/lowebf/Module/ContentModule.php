<?php

namespace lowebf\Module;

use lowebf\Data\ContentUnit;

class ContentModule extends Module
{
    private function getContentUnitPath(string $contentUnitId) : string
    {
        return $this->env->asAbsoluteDataPath("content/$contentUnitId");
    }

    public function load(string $contentUnitId) : ContentUnit
    {
        $path = $this->getContentUnitPath($contentUnitId);
        return ContentUnit::loadFromFile($this->env, $path);
    }

    public function loadOrCreate(string $contentUnitId) : ContentUnit
    {
        $path = $this->getContentUnitPath($contentUnitId);
        return ContentUnit::loadFromFileOrCreate($this->env, $path);
    }

    public function delete(string $contentUnitId) {}
}
