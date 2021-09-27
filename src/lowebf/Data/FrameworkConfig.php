<?php

namespace lowebf\Data;

class FrameworkConfig
{
    const CACHE_OPTION = "cacheEnabled";
    const DEBUG_OPTION = "debugEnabled";
    const ROUTE_PATH_OPTION = "routePath";
    const THUMBNAIL_OPTION = "thumbnailSize";
    const POST_DESCRIPTION_LENGTH_OPTION = "postDescriptionLength";
    const POSTS_PER_PAGE_AMOUNT_OPTION = "postsPerPage";

    const DEFAULT_CACHING_ENABLED = true;
    const DEFAULT_DEBUG_ENABLED = false;
    const DEFAULT_POSTS_PER_PAGE = 15;
    const DEFAULT_POST_DESCRIPTION_LENGTH = 50;
    const DEFAULT_THUMBNAIL_SIZE = 128;
    const DEFAULT_ROUTE_SCRIPT = "/route.php";

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

        return self::DEFAULT_CACHING_ENABLED;
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

        return self::DEFAULT_DEBUG_ENABLED;
    }

    public function setDebugEnabled(bool $state)
    {
        $this->data[self::DEBUG_OPTION] = $state;
    }

    public function getPostsPerPageAmount() : int
    {
        if (isset($this->data[self::POSTS_PER_PAGE_AMOUNT_OPTION])) {
            return $this->data[self::POSTS_PER_PAGE_AMOUNT_OPTION];
        }

        return self::DEFAULT_POSTS_PER_PAGE;
    }

    public function getPostDescriptionLength() : int
    {
        if (isset($this->data[self::POST_DESCRIPTION_LENGTH_OPTION])) {
            return $this->data[self::POST_DESCRIPTION_LENGTH_OPTION];
        }

        return self::DEFAULT_POST_DESCRIPTION_LENGTH;
    }

    public function getThumbnailSize() : int
    {
        if (isset($this->data[self::THUMBNAIL_OPTION])) {
            return $this->data[self::THUMBNAIL_OPTION];
        }

        return self::DEFAULT_THUMBNAIL_SIZE;
    }

    public function getRoutePath() : string
    {
        if (isset($this->data[self::ROUTE_PATH_OPTION])) {
            return $this->data[self::ROUTE_PATH_OPTION];
        }

        return self::DEFAULT_ROUTE_SCRIPT;
    }

    public function setRoutePath(string $path)
    {
        $this->data[self::ROUTE_PATH_OPTION] = $path;
    }
}
