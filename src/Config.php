<?php

namespace lowebf;

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
        $this->globals = Util::load_json_file($this->path . '/globals.json');
    }

    public function getTwig()
    {
        return Util::load_json_file($this->path . '/twig.json');
    }
}
