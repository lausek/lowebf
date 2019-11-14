<?php

namespace lowebf;

class Environment {
    public static $instance;
    public $data;

    public static function getInstance(): Environment
    {
        if(self::$instance === null) {
            self::$instance = Environment::create();
        }
        return self::$instance;
    }

    public static function create(string $opt_path = null): Environment
    {
        return new Environment($opt_path);
    }

    private function __construct(string $opt_path = null)
    {
        $path = $opt_path ?? $_SERVER['DOCUMENT_ROOT'];
        $this->data = new DataProvider("$path/data");
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getPostAttributes()
    {
        return $_POST;
    }

    public function getGetAttributes()
    {
        return $_GET;
    }

    public function asAbsolutePath(string $suffix): string
    {
        return $this->getRoot() . $suffix;
    }

    public function getRoot(): string
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }
}
