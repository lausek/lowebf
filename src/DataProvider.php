<?php

namespace lowebf;

class DataProvider {
    public $path;
    public $cache;
    public $config;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->cache = new Cache('cache');
    }

    public function getTwigSettings()
    {
        return [
            'debug' => true,
            //'cache' => Config::getRoot() . '/cache/twig',
        ];
    }

}
