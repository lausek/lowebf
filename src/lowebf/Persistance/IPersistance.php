<?php

namespace lowebf\Persistance;

use lowebf\Environment;

interface IPersistance
{
    public function load(Environment $env, string $path) : array;

    public function save(Environment $env, string $path, array $data);
}
