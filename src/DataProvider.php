<?php

namespace lowebf;

class DataProvider {
    public $path;
    public $cache;
    public $config;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->cache = new Cache(Config::getRoot() . '/cache');
        $this->config = new Config("$path/config");
    }
}
