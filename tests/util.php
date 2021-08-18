<?php

namespace lowebf\Test;

use lowebf\Environment;

function dummy(string $path, string $content = null)
{
    // only allow creation in /tmp
    assert(strpos($path, "/tmp") === 0);

    if (str_ends_with($path, "/")) {
        // ignore errors if file exists already
        @mkdir($path, 0755, true);
    } else {
        // remove filename from directory path
        $dirPath = substr($path, 0, strrpos($path, "/"));
        $content = $content !== null ? $content : "";
        @mkdir($dirPath, 0755, true);
        // create a dummy file
        file_put_contents($path, $content);
    }
}

function tmpdir() : string
{
    return "/tmp";
}

