<?php

namespace lowebf\Data;

interface IStorable
{
    public function get(string $name);

    public function set(string $name, $value);

    public function exists(string $name) : bool;

    public function unset(string $name);
}
