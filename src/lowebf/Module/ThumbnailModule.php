<?php

namespace lowebf\Module;

use lowebf\Result;

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

    /**
     * @return Result<string>
     * */
    private function generateThumbnailImagick(string $subpath) : Result
    {
        $originalPath = $this->env->route()->pathFor($subpath);
        $height = 128;
        $width = 128;

        $image = new \Imagick();
        $result = $this->env->filesystem()->loadFile($originalPath);
        if ($result->isError()) {
            return $result;
        }

        // load image from filesystem
        $image->readImageBlob($result->unwrap());
        // remove metadata
        $image->stripImage();
        // scale to thumbnail size with $bestfit option enabled
        $image->thumbnailImage($width, $height, true);

        $blob = $image->getImageBlob();

        $image->destroy();

        return Result::ok($blob);
    }

    /**
     * @return Result<string>
     * */
    private function generateThumbnailGd(string $subpath) : Result
    {
        $originalPath = $this->env->route()->pathFor($subpath);
        $result = $this->env->filesystem()->loadFile($originalPath);
        if ($result->isError()) {
            return $result;
        }

        $content = $result->unwrap();

        $oldImage = imagecreatefromstring($content);
        $extension = pathinfo($subpath, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        $toHeight = 128;
        $toWidth = 128;
        $fromHeight = (int)imagesy($oldImage);
        $fromWidth = (int)imagesx($oldImage);
        // take thumbnail from center of image
        $fromX = intdiv($fromWidth, 2) - intdiv($toWidth, 2);
        $fromY = intdiv($fromHeight, 2) - intdiv($toHeight, 2);

        // TODO: check coordinate bounds

        $newImage = imagecreatetruecolor($toWidth, $toHeight);
        $transparentColor = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        imagefill($newImage, 0, 0, $transparentColor);
        imagecopyresampled(
            $newImage,
            $oldImage,
            0,
            0,
            $fromX,
            $fromY,
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

        return Result::ok($buffer);
    }

    public function generateThumbnailFor(string $subpath) : string
    {
        if (extension_loaded("imagick")) {
            $result = $this->generateThumbnailImagick($subpath);
        } else {
            $result = $this->generateThumbnailGd($subpath);
        }

        $content = $result->unwrap();

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
