<?php

namespace lowebf\Module;

use lowebf\Data\ContentUnit;
use lowebf\Result;

class ContentModule extends Module
{
    private function getContentUnitPath(string $contentUnitId) : string
    {
        return $this->env->asAbsoluteDataPath("content/$contentUnitId");
    }

    /**
     * @return Result<ContentUnit>
     * */
    public function load(string $contentUnitId) : Result
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
