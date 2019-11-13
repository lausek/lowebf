<?php

namespace lowebf;

final class Util{

    public static function readDir($dir)
    {
        return array_filter(
            scandir($dir),
            function ($name)
            {
                return strpos($name, '.') !== 0;
            }
        );
    }

}
