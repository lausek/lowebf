<?php

namespace lowebf\Data;

use function lowebf\extractAttributesFromPath;
use lowebf\Data\Gallery;
use lowebf\Environment;
use lowebf\Error\FileNotFoundException;
use lowebf\Result;

class Post {
    /** @var Environment */
	    private $env;

    /** @var string */
	    private $path;

    /** @var string */
	    private $id;

    /** @var string */
	    private $title;

    /** @var string */
	    private $date;

    /** @var ContentUnit|null */
	    private $contentUnit = null;

    private function __construct(Environment $env, string $path, string $title, string $date)
    {
        $this->id = pathinfo($path, PATHINFO_FILENAME);
        $this->env = $env;
        $this->path = $path;
        $this->title = $title;
        $this->date = $date;
    }

    /**
     * @return Result<Post>
     * */
    public static function loadFromFile(Environment $env, string $path) : Result
    {
        if (!$env->hasFile($path)) {
            return Result::error(new FileNotFoundException($path));
        }

        $attributes = extractAttributesFromPath($path);
        $title = $attributes["title"];
        $date = $attributes["date"];

        // check if the file extension is supported.
        // this throws an exception if not.
        try {
            ContentUnit::getPersistorFromPath($path);
        } catch (\Exception $e) {
            return Result::error($e);
        }

        $post = new Post($env, $path, $title, $date);
        return Result::ok($post);
    }

    public static function loadFromFileOrCreate(Environment $env, string $path) : Post
    {
        // TODO: loading the file twice is inefficient
        if (self::loadFromFile($env, $path)->isError()) {
            ContentUnit::loadFromFileOrCreate($env, $path)->save();
        }

        return self::loadFromFile($env, $path)->unwrap();
    }

    public function triggerLoading()
    {
        if ($this->contentUnit === null) {
            $this->contentUnit = ContentUnit::loadFromFile($this->env, $this->path)->unwrap();

            if ($this->contentUnit->exists("title")) {
                $this->title = $this->contentUnit->get("title");
            }
        }
    }

    public function __isset(string $name) : bool
    {
        if ($name === "content") {
            return true;
        }

        // if the attribute is something else -> load content from file
        $this->triggerLoading();
        return $this->contentUnit->exists($name);
    }

    public function __get(string $name)
    {
        if ($name === "content") {
            return $this->getContent();
        }

        // if the attribute is something else -> load content from file
        $this->triggerLoading();
        return $this->contentUnit->get($name);
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getAuthor() : ?string
    {
        $this->triggerLoading();
        return $this->contentUnit->get("author");
    }

    public function getContentRaw() : string
    {
        $this->triggerLoading();
        return $this->contentUnit->get("content", "");
    }

    public function getContent(bool $useAbsoluteUrls = false) : string
    {
        $this->triggerLoading();
        return $this->contentUnit->getContent($useAbsoluteUrls);
    }

    public function getDate() : \DateTime
    {
        $dateTime = \DateTime::createFromFormat("Y-m-d", $this->date);

        if ($dateTime === false) {
            throw new \Exception("invalid date format");
        }

        return $dateTime;
    }

    public function getDescription($maxLen = null) : string
    {
        if ($maxLen === null) {
            $maxLen = $this->env->config()->lowebf()->getPostDescriptionLength();
        }

        $description = $this->getContent();
        $description = strip_tags($description);

        if ($maxLen < strlen($description)) {
            return substr($description, 0, $maxLen - 1) . '…';
        }

        return $description;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function getGallery() : ?Gallery
    {
        $this->triggerLoading();

        $galleryId = $this->contentUnit->get("gallery");

        if ($galleryId !== null) {
            return $this->env->galleries()->load($galleryId)->unwrap();
        }

        return null;
    }

    public function setAuthor(string $author) : Post
    {
        $this->triggerLoading();
        $this->contentUnit->set("author", $author);

        return $this;
    }

    public function setContent(string $content) : Post
    {
        $this->triggerLoading();
        $this->contentUnit->set("content", $content);
        return $this;
    }

    public function setDate(string $date) : Post
    {
        $this->triggerLoading();
        $this->contentUnit->set("date", $date);
        return $this;
    }

    public function setTitle(string $title) : Post
    {
        $this->triggerLoading();
        $this->contentUnit->set("title", $title);
        return $this;
    }

    public function save()
    {
        $this->triggerLoading();
        $this->contentUnit->save();
    }
}
