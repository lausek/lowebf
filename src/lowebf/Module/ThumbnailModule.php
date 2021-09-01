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

    private function generateThumbnailImagick(string $subpath) : string
    {
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

        $blob = $image->getImageBlob();

        $image->destroy();

        return $blob;
    }

    private function generateThumbnailGd(string $subpath) : string
    {
        $originalPath = $this->env->route()->pathFor($subpath);
        $content = $this->env->filesystem()->loadFile($originalPath);
        $oldImage = imagecreatefromstring($content);
        $extension = pathinfo($subpath, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        $toHeight = 128;
        $toWidth = 128;
        $fromHeight = (int)imagesy($oldImage);
        $fromWidth = (int)imagesx($oldImage);

        $newImage = imagecreatetruecolor($toWidth, $toHeight);
        $transparentColor = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        imagefill($newImage, 0, 0, $transparentColor);
        imagecopyresampled(
            $newImage,
            $oldImage,
            0,
            0,
            0,
            0,
            $toWidth,
            $toHeight,
            $fromWidth,
            $fromHeight,
        );

        $buffer = "";

        try {
            ob_start();

            switch ($extension) {
                case "gif":
                    imagegif($newImage);
                    break;

                case "png":
                    imagepng($newImage);
                    break;

                case "jpeg":
                // fallthrough
                case "jpg":
                    imagejpeg($newImage);
                    break;

                default:
                    throw new \Exception("unknown file extension: $extension");
            }

            $buffer = ob_get_contents();
        } finally {
            ob_end_clean();
        }

        return $buffer;
    }

    public function generateThumbnailFor(string $subpath) : string
    {
        if (extension_loaded("imagick")) {
            $content = $this->generateThumbnailImagick($subpath);
        } else {
            $content = $this->generateThumbnailGd($subpath);
        }

        $this->env->cache()->set($this->cacheKey($subpath), $content);

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
