<?php

namespace lowebf;

function load_json_file(string $path): array
{
    $content = file_get_contents($path);
    if($content === false) {
        return [];
    }
    $json = json_decode($content);
    return $json !== null ? (array)$json : [];
}

final class Config {

    const SITE_TITLE = '';
    const SITE_URL = '';

    const NEWS_PAGE_COUNT = 10;
    const NEWS_SHORT_LENGTH = 80;

    public $path;
    public $globals;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->globals = load_json_file($this->path . '/globals.json');
    }

    public function getTwig()
    {
        return load_json_file($this->path . '/twig.json');
    }
}
