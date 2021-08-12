<?php

namespace lowebf\Data;

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
        $title = "";
        $date = "";

        return [
            "title" => $title,
            "date" => \DateTime::createFromFormat("Y-m-d", $date),
        ];
    }

    public static function loadFromFile(string $path) : ?Post
    {
        $contentUnit = ContentUnit::loadFromFile($path);
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

    public static function loadFromFileOrCreate(string $path) : ?Post
    {
        $contentUnit = ContentUnit::loadFromFileOrCreate($path);
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
        return $this;
    }
}
