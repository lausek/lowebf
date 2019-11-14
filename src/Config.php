<?php

namespace lowebf;

final class Config {

    const SITE_TITLE = '';
    const SITE_URL = '';

    const NEWS_PAGE_COUNT = 10;
    const NEWS_SHORT_LENGTH = 80;

    public $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getTwig()
    {
        $content = file_get_contents($this->path . '/twig.json');
        if($content === false) {
            return [];
        }
        return (array)json_decode($content);
    }

    public function getRoot(): string
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }
}
