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

    public function getServerName() : string
    {
        // TODO: access superglobals through lowebf
        return isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : "localhost";
    }

    public function getScheme() : string
    {
        // TODO: access superglobals through lowebf
        return isset($_SERVER["PROTOCOL"]) ? $_SERVER["PROTOCOL"] : "https";
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
                // fallthrough
            case "galleries":
                return $this->env->asAbsoluteDataPath($subpath);
        }

        throw new FileNotFoundException($subpath);
    }

    /**
        * @throws FileNotFoundException */
    public function urlFor(string $subpath) : string
    {
        $scriptPath = $this->env->config()->lowebf()->getRoutePath();
        $scriptPath = ltrim($scriptPath, "/");
        $subpath = ltrim($subpath, "/");

        // check if subpath is inside an expected rootDirectory
        // raises an exception if not
        $this->pathFor($subpath);

        return "/$scriptPath?x=/$subpath";
    }

    public function absoluteUrlFor(string $subpath) : string
    {
        $scheme = $this->getScheme();
        $host = $this->getServerName();

        $url = $this->urlFor($subpath);

        return "$scheme://$host$url";
    }

    public function provideAndExit(string $subpath)
    {
        $path = $this->pathFor($subpath);
        $filename = pathinfo($path, PATHINFO_BASENAME);

        if (!$this->env->hasFile($path)) {
            $this->env->runtime()->raiseFileNotFoundAndExit();

            // needed for testing
            return;
        }

        if (!$this->env->config()->lowebf()->isCacheEnabled()) {
            // four hours
            $age = 60 * 60 * 4;
            $this->env->runtime()->setHeader("Cache-Control", "public, max-age=$age");
        }

        $this->env->runtime()->setContentTypeFromFile($path);
        $this->env->runtime()->setHeader("Content-Disposition", "inline; filename=\"$filename\"");
        $this->env->runtime()->sendFromFile($this->env, $path);
        $this->env->runtime()->exit();
    }
}
