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

    /**
     * @return array
     * */
    public function loadGalleries() : array
    {
        $galleryDirectory = $this->env->asAbsoluteDataPath("galleries");
        $galleries = [];

        foreach ($this->env->listDirectory($galleryDirectory)->unwrapOr([]) as $galleryId => $_) {
            $galleries[] = $this->load($galleryId)->unwrap();
        }

        usort(
            $galleries,
            function ($a, $b) {
                $idA = $a->getId();
                $idB = $b->getId();

                if ($idA === $idB) {
                    return 0;
                }

                return $idA < $idB ? 1 : -1;
            }
        );

        return $galleries;
    }
}
