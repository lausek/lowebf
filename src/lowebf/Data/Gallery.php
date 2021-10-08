<?php

namespace lowebf\Data;

use lowebf\Environment;
use lowebf\Error\FileNotFoundException;
use lowebf\Result;

class Gallery
{
    /** @var Environment */
	    private $env;

    /** @var string */
	    private $path;

    /** @var string */
	    private $title;

    /** @var string */
	    private $date;

    private function __construct(Environment $env, string $path, string $title, string $date)
    {
        $this->id = pathinfo($path, PATHINFO_FILENAME);
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
     * @return Result<Gallery>
     * */
    public static function loadFromPath(Environment $env, string $path) : Result
    {
        if (!$env->hasFile($path)) {
            return Result::error(new FileNotFoundException($path));
        }

        $gallery = self::loadFromPathOrCreate($env, $path);
        return Result::ok($gallery);
    }

    public static function loadFromPathOrCreate(Environment $env, string $path) : Gallery
    {
        $attributes = self::extractAttributesFromPath($path);
        $title = $attributes["title"];
        $date = $attributes["date"];

        return new Gallery($env, $path, $title, $date);
    }

    public function save()
    {
        if (!$this->env->hasFile($this->path)) {
            $fullPath = rtrim($this->path, "/") . "/.";
            $this->env->filesystem()->mkdir($fullPath);
        }
    }

    public function getId() : string
    {
        return basename($this->path);
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
}
