<?php

namespace lowebf\Persistance;

use lowebf\Environment;
use lowebf\Result;

interface IPersistance
{
    /**
     * @return Result<string>
     * */
    public function load(Environment $env, string $path) : Result;

    public function save(Environment $env, string $path, array $data);
}
