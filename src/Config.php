<?php

declare(strict_types=1);

namespace lowebf;

final class Config
{
    public const SITE_TITLE = '';
    public const SITE_URL = '';

    public const NEWS_PAGE_COUNT = 10;
    public const NEWS_SHORT_LENGTH = 80;

    public $path;
    public $globals;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->globals = util()->load_json_file($this->path . '/globals.json');
    }

    public function getTwig()
    {
        return util()->load_json_file($this->path . '/twig.json');
    }
}
