<?php

namespace lowebf\Data;

class Post {

    /* @var ContentUnit */
	private $contentUnit;

    function __construct(string $title, \DateTime $date, string $content) {

    }

    public function loadFromFile(string $path): ?Post {
        return null;
    }

    public function getAuthor(): ?string {
        return $this->contentUnit->get("author");
    }

    public function getContent(): string {
        return $this->contentUnit->get("content");
    }

    public function getDate(): \DateTime {
        $date = $this->contentUnit->get("date");
        $dateTime = \DateTime::createFromFormat("Y-m-d", $date);

        if($dateTime === false) {
            throw new \Exception("invalid date format");
        }

        return $dateTime;
    }

    public function getTitle(): string {
        return $this->contentUnit->get("title");
    }

    public function setAuthor(string $author): Post {
        return $this;
    }
}
