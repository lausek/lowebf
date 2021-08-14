<?php

namespace lowebf\Data;

use lowebf\Environment;

use Michelf\Markdown;

class Post
{
    /* @var ContentUnit */
	    private $contentUnit;

    private function __construct(ContentUnit $contentUnit, array $attributes)
    {
        $this->contentUnit = $contentUnit;

        foreach ($attributes as $key => $value) {
            if (!$this->contentUnit->exists($key)) {
                $this->contentUnit->set($key, $value);
            }
        }
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

    public static function loadFromFile(Environment $env, string $path) : ?Post
    {
        $contentUnit = ContentUnit::loadFromFile($env, $path);
        $attributes = self::extractAttributesFromPath($path);

        /*
        $title = $attributes["title"];
        $date = $attributes["date"];

        if($contentUnit->exists("title")) {
            $title = $contentUnit->get("title");
        }

        if($contentUnit->exists("date")) {
            $date = $contentUnit->get("date");
        }
        $post = new Post($title, $date, $content);
        */

        return new Post($contentUnit, $attributes);
    }

    public static function loadFromFileOrCreate(Environment $env, string $path) : ?Post
    {
        $contentUnit = ContentUnit::loadFromFileOrCreate($env, $path);
        $attributes = self::extractAttributesFromPath($path);

        return new Post($contentUnit, $attributes);
    }

    public function getAuthor() : ?string
    {
        return $this->contentUnit->get("author");
    }

    public function getContent() : string
    {
        return $this->contentUnit->get("content");
    }

    public function getContentHtml() : string
    {
        return rtrim(Markdown::defaultTransform($this->getContent()), "\n ");
    }

    public function getDate() : \DateTime
    {
        $date = $this->contentUnit->get("date");
        $dateTime = \DateTime::createFromFormat("Y-m-d", $date);

        if ($dateTime === false) {
            throw new \Exception("invalid date format");
        }

        return $dateTime;
    }

    public function getTitle() : string
    {
        return $this->contentUnit->get("title");
    }

    public function setAuthor(string $author) : Post
    {
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

    public function save() {
        $this->contentUnit->save();
    }
}
