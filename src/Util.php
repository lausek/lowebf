<?php

namespace lowebf;

final class Util{

    public static function read_dir($dir): array
    {
        return array_filter(
            scandir($dir),
            function ($name)
            {
                return strpos($name, '.') !== 0;
            }
        );
    }

    public static function load_json_file(string $path): array
    {
        $content = file_get_contents($path);
        if($content === false) {
            return [];
        }
        $json = json_decode($content);
        return $json !== null ? (array)$json : [];
    }
}
