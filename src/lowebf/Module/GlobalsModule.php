<?php

namespace lowebf\Module;

use lowebf\Environment;
use lowebf\Result;

class GlobalsModule extends Module
{
    /** @var array|null */
    protected $getGlobals = null;
    /** @var array|null */
    protected $postGlobals = null;
    /** @var array|null */
    protected $serverGlobals = null;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->getGlobals = $_GET;
        $this->postGlobals = $_POST;
        $this->serverGlobals = $_SERVER;
    }

    public function get(string $key) : Result
    {
        if (!isset($this->getGlobals[$key])) {
            return Result::error(new \Exception("get key not found: $key"));
        }

        return Result::ok($this->getGlobals[$key]);
    }

    public function post(string $key) : Result
    {
        if (!isset($this->postGlobals[$key])) {
            return Result::error(new \Exception("post key not found: $key"));
        }

        return Result::ok($this->postGlobals[$key]);
    }

    public function server(string $key) : Result
    {
        if (!isset($this->serverGlobals[$key])) {
            return Result::error(new \Exception("server key not found: $key"));
        }

        return Result::ok($this->serverGlobals[$key]);
    }
}
