<?php

namespace lowebf\Data;

class FrameworkConfig
{
    const CACHE_OPTION = "cacheEnabled";
    const DEBUG_OPTION = "debugEnabled";
    const ROUTE_PATH_OPTION = "routePath";
    const THUMBNAIL_OPTION = "thumbnailSize";

    /** @var array */
    private $data;

    public function __construct(array &$data)
    {
        $this->data = &$data;
    }

    public function isCacheEnabled() : bool
    {
        if (isset($this->data[self::CACHE_OPTION])) {
            return $this->data[self::CACHE_OPTION] === true;
        }

        return true;
    }

    public function setCacheEnabled(bool $state)
    {
        $this->data[self::CACHE_OPTION] = $state;
    }

    public function isDebugEnabled() : bool
    {
        if (isset($this->data[self::DEBUG_OPTION])) {
            return $this->data[self::DEBUG_OPTION] === true;
        }

        return false;
    }

    public function setDebugEnabled(bool $state)
    {
        $this->data[self::DEBUG_OPTION] = $state;
    }

    public function getThumbnailSize() : int
    {
        if (isset($this->data[self::THUMBNAIL_OPTION])) {
            return $this->data[self::THUMBNAIL_OPTION];
        }

        return 128;
    }

    public function getRoutePath() : string
    {
        if (isset($this->data[self::ROUTE_PATH_OPTION])) {
            return $this->data[self::ROUTE_PATH_OPTION];
        }

        return "/route.php";
    }

    public function setRoutePath(string $path)
    {
        $this->data[self::ROUTE_PATH_OPTION] = $path;
    }
}
