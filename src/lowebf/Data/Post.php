<?php

namespace lowebf\Data;

use lowebf\Environment;
use lowebf\Error\FileNotFoundException;
use lowebf\Error\NotPersistableException;
use lowebf\Parser\Markdown;

class Post
{
    /** @var Environment */
	    private $env;

    /** @var string */
	    private $path;

    /** @var string */
	    private $title;

    /** @var string */
	    private $date;

    /** @var ContentUnit|null */
	    private $contentUnit = null;

    private function __construct(Environment $env, string $path, string $title, string $date)
    {
        $this->env = $env;
        $this->path = $path;
        $this->title = $title;
        $this->date = $date;
    }

    public static function extractAttributesFromPath(string $path) : array
    {
        $fileName = pathinfo($path, PATHINFO_FILENAME);
        $date = substr($fileName, 0, 10);

        $title = substr($fileName, 11);
        $title = str_replace("-", " ", $title);
        $title = ucwords($title);

        return [
            "date" => $date,
            "title" => $title,
        ];
    }

    /**
     * @throws FileNotFoundException
     * @throws NotPersistableException
     * */
    public static function loadFromFile(Environment $env, string $path) : Post
    {
        if (!$env->hasFile($path)) {
            throw new FileNotFoundException($path);
        }

        $attributes = self::extractAttributesFromPath($path);
        $title = $attributes["title"];
        $date = $attributes["date"];

        // check if the file extension is supported.
        // this throws an exception if not.
        ContentUnit::getPersistorFromPath($path);

        return new Post($env, $path, $title, $date);
    }

    public static function loadFromFileOrCreate(Environment $env, string $path) : Post
    {
        if (!$env->hasFile($path)) {
            ContentUnit::loadFromFileOrCreate($env, $path)->save();
        }

        return self::loadFromFile($env, $path);
    }

    public function loadContentUnit()
    {
        if ($this->contentUnit === null) {
            $this->contentUnit = ContentUnit::loadFromFile($this->env, $this->path);
        }
    }

    public function __isset(string $name) : bool
    {
        if ($name === "content") {
            return true;
        }

        // if the attribute is something else -> load content from file
        $this->loadContentUnit();
        return $this->contentUnit->exists($name);
    }

    public function __get(string $name)
    {
        if ($name === "content") {
            return $this->getContent();
        }

        // if the attribute is something else -> load content from file
        $this->loadContentUnit();
        return $this->contentUnit->get($name);
    }

    public function getId() : string
    {
        $title = str_replace(" ", "-", $this->title);
        $title = strtolower($title);
        return $this->date . "-" . $title;
    }

    public function getAuthor() : ?string
    {
        $this->loadContentUnit();
        return $this->contentUnit->get("author");
    }

    public function getContentRaw() : string
    {
        $this->loadContentUnit();
        return $this->contentUnit->get("content", "");
    }

    public function getContent() : string
    {
        $markdown = new Markdown($this->env);

        return rtrim($markdown->transform($this->getContentRaw()), "\n ");
    }

    public function getDate() : \DateTime
    {
        $dateTime = \DateTime::createFromFormat("Y-m-d", $this->date);

        if ($dateTime === false) {
            throw new \Exception("invalid date format");
        }

        return $dateTime;
    }

    public function getDescription(int $maxLen = 50) : string
    {
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

    public function setAuthor(string $author) : Post
    {
        $this->loadContentUnit();
        $this->contentUnit->set("author", $author);

        return $this;
    }

    public function setContent(string $content) : Post
    {
        $this->contentUnit->set("content", $content);
        return $this;
    }

    public function setDate(string $date) : Post
    {
        $this->contentUnit->set("date", $date);
        return $this;
    }

    public function setTitle(string $title) : Post
    {
        $this->contentUnit->set("title", $title);
        return $this;
    }

    public function save()
    {
        $this->loadContentUnit();
        $this->contentUnit->save();
    }
}
