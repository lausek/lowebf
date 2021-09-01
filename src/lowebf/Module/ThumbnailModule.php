<?php

namespace lowebf\Module;

class ThumbnailModule extends Module
{
    public function cacheKey(string $subpath) : string
    {
        $subpath = ltrim($subpath, "/");
        return "thumb/$subpath";
    }

    public function cachePathFor(string $subpath) : string
    {
        return $this->env->cache()->getPath($this->cacheKey($subpath));
    }

    public function isCached(string $subpath) : bool
    {
        $cachePath = $this->cachePathFor($subpath);
        return $this->env->hasFile($cachePath);
    }

    public function generateThumbnailFor(string $subpath) : string
    {
        if (!extension_loaded("imagick")) {
            throw new \Exception("imagick extension is not installed");
        }

        $originalPath = $this->env->route()->pathFor($subpath);
        $height = 128;
        $width = 128;

        $image = new \Imagick();

        // load image from filesystem
        $image->readImageBlob($this->env->filesystem()->loadFile($originalPath));
        // remove metadata
        $image->stripImage();
        // scale to thumbnail size with $bestfit option enabled
        $image->thumbnailImage($width, $height, true);

        $this->env->cache()->set($this->cacheKey($subpath), $image->getImageBlob());

        $image->destroy();

        return $this->cachePathFor($subpath);
    }

    /**
     * Get a thumbnail path for a given subpath. If the thumbnail is not
     * cached yet it will be generated.
     *
     * @return the cache path to which the thumbnail was written */
    public function pathFor(string $subpath) : string
    {
        if (!$this->env->cache()->exists($this->cacheKey($subpath))) {
            return $this->generateThumbnailFor($subpath);
        }

        return $this->cachePathFor($subpath);
    }
}
