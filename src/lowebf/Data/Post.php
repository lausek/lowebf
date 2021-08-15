<?php

namespace lowebf\Data;

use lowebf\Environment;

use Michelf\Markdown;

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

    // TODO: add attribute for lazy loading values like 'author' and 'content'

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
            "title" => $title,
            "date" => $date,
        ];
    }

    public static function loadFromFile(Environment $env, string $path) : Post
    {
        if(!$env->hasFile($path)) {
            throw new \Exception("file does not exist: $path");
        }

        $attributes = self::extractAttributesFromPath($path);
        $title = $attributes["title"];
        $date = $attributes["date"];

        return new Post($env, $path, $title, $date);
    }

    public static function loadFromFileOrCreate(Environment $env, string $path) : Post
    {
        if(!$env->hasFile($path)) {
            ContentUnit::loadFromFileOrCreate($env, $path);
        }

        return self::loadFromFile($env, $path);
    }

    public function loadContentUnit()
    {
        if($this->contentUnit === null) {
            $this->contentUnit = ContentUnit::loadFromFile($this->env, $this->path);
        }
    }

    public function getAuthor() : ?string
    {
        $this->loadContentUnit();
        return $this->contentUnit->get("author");
    }

    public function getContent() : string
    {
        $this->loadContentUnit();
        return $this->contentUnit->get("content");
    }

    public function getContentHtml() : string
    {
        return rtrim(Markdown::defaultTransform($this->getContent()), "\n ");
    }

    public function getDate() : \DateTime
    {
        $dateTime = \DateTime::createFromFormat("Y-m-d", $this->date);

        if ($dateTime === false) {
            throw new \Exception("invalid date format");
        }

        return $dateTime;
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
        $this->contentUnit->save();
    }
}
