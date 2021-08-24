<?php

namespace lowebf\Module;

use lowebf\Error\FileNotFoundException;

class RouteModule extends Module
{
    private function getRootDirectory(string $subpath) : string
    {
        $subpath = ltrim($subpath, "/");
        $firstSeparatorIndex = strpos($subpath, "/");

        if ($firstSeparatorIndex === false) {
            return $subpath;
        }

        return substr($subpath, 0, $firstSeparatorIndex);
    }

    public function pathFor(string $subpath) : string
    {
        $subpath = ltrim($subpath, "/");
        $rootDirectory = $this->getRootDirectory($subpath);

        switch ($rootDirectory) {
            case "css":
                // fallthrough
            case "img":
                // fallthrough
            case "js":
                return $this->env->asAbsolutePath("site/$subpath");

            case "cache":
                return $this->env->asAbsolutePath("$subpath");

            case "media":
                return $this->env->asAbsoluteDataPath($subpath);
        }

        throw new FileNotFoundException($subpath);
    }

    public function urlFor(string $subpath) : string
    {
        $scriptPath = $this->env->config()->getRouteScriptPath();
        $scriptPath = ltrim($scriptPath, "/");
        $subpath = ltrim($subpath, "/");

        // check if subpath is inside an expected rootDirectory
        // raises an exception if not
        $this->pathFor($subpath);

        return "/$scriptPath?x=/$subpath";
    }

    public function absoluteUrlFor(string $subpath) : string
    {
        $protocol = isset($_SERVER["PROTOCOL"]) ? $_SERVER["PROTOCOL"] : "https";
        $host = isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : "localhost";

        $url = $this->urlFor($subpath);

        return "$protocol://$host$url";
    }

    public function provideAndExit(string $subpath)
    {
        $path = $this->pathFor($subpath);

        if (!$this->env->hasFile($path)) {
            $this->env->runtime()->exit(404);
        }

        $this->env->runtime()->setContentTypeFromFile($path);
        $this->env->runtime()->sendFromFile($this->env, $path);
        $this->env->runtime()->exit();
    }
}
