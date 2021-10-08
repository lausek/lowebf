<?php

namespace lowebf\Module;

use lowebf\Data\Gallery;
use lowebf\Result;

class GalleryModule extends Module
{
    /**
     * @return Result<Gallery>
     * */
    public function load(string $galleryId) : Result
    {
        $galleryPath = $this->env->asAbsoluteDataPath("/galleries/$galleryId");
        return Gallery::loadFromPath($this->env, $galleryPath);
    }

    public function loadOrCreate(string $galleryId) : Gallery
    {
        $galleryPath = $this->env->asAbsoluteDataPath("/galleries/$galleryId");
        return Gallery::loadFromPathOrCreate($this->env, $galleryPath);
    }
}
