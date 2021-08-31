<?php

namespace lowebf;

use lowebf\Filesystem\CoreFilesystem;
use lowebf\Filesystem\Filesystem;
use lowebf\Error\FileNotFoundException;
use lowebf\Module\CacheModule;
use lowebf\Module\ConfigModule;
use lowebf\Module\ContentModule;
use lowebf\Module\DownloadModule;
use lowebf\Module\PostModule;
use lowebf\Module\RouteModule;
use lowebf\Module\ViewModule;

class Environment
{
    /** @var PhpRuntime */
	    protected $phpRuntime = null;
    /** @var string */
	    protected $rootPath;
    /** @var string */
	    protected $dataPath;
    /** @var CoreFilesystem */
    protected $filesystem;

    /** @var CacheModule */
	    protected $cacheModule = null;
    /** @var ConfigModule */
	    protected $configModule = null;
    /** @var ContentModule */
	    protected $contentModule = null;
    /** @var DownloadModule */
	    protected $downloadModule = null;
    /** @var PostModule */
	    protected $postModule = null;
    /** @var RouteModule */
	    protected $routeModule = null;
    /** @var ViewModule */
	    protected $viewModule = null;

    public static function getInstance() : Environment
    {
        $rootDirectory = ini_get("lowebf_root");

        if ($rootDirectory === false) {
            $rootDirectory = $_SERVER["DOCUMENT_ROOT"];
            $rootDirectory = rtrim($rootDirectory, "/");
            $rootDirectory = rtrim($rootDirectory, "/site/public");
        }

        return new Environment($rootDirectory);
    }

    public function __construct(string $rootPath, string $dataPath = null)
    {
        // TODO: also trim backslash '\'
        $rootPath = rtrim($rootPath, "/");

        if ($dataPath === null) {
            $dataPath = "$rootPath/data";
        }

        $this->rootPath = $rootPath;
        $this->dataPath = $dataPath;

        $this->filesystem = new Filesystem($this);
        $this->phpRuntime = new PhpRuntime($this);
    }

    public function asRealpath(string $path) : string
    {
        return $path;
        /*
        $realPath = realpath($path);

        if ($realPath === false) {
            throw new \Exception("cannot create realpath '$path': path does not exist.");
        }

        return $realPath;
         */
    }

    public function asAbsolutePath(string $subpath) : string
    {
        $left = rtrim($this->getRootPath(), "/");
        $right = ltrim($subpath, "/");
        $joined = "$left/$right";
        return $this->asRealpath($joined);
    }

    public function asAbsoluteDataPath(string $subpath) : string
    {
        $left = rtrim($this->getDataPath(), "/");
        $right = ltrim($subpath, "/");
        $joined = "$left/$right";
        return $this->asRealpath($joined);
    }

    public function getRootPath() : string
    {
        return $this->rootPath;
    }

    public function getDataPath() : string
    {
        return $this->dataPath;
    }

    // TODO: deprecated
    public function getLastModified(string $path) : int
    {
        return $this->filesystem()->lastModified($path);
    }

    // TODO: deprecated
    public function hasFile(string $path) : bool
    {
        return $this->filesystem()->exists($path);
    }

    // TODO: deprecated
    public function loadFile(string $path) : string
    {
        return $this->filesystem()->loadFile($path);
    }

    // TODO: deprecated
    public function saveFile(string $path, $content)
    {
        $this->filesystem()->saveFile($path, $content);
    }

    // TODO: deprecated
    public function makeAllDirectories(string $path)
    {
        $this->filesystem()->mkdir($path);
    }

    // TODO: deprecated
    public function sendFile(string $path)
    {
        $this->filesystem()->sendFile($path);
    }

    /**
     * Extension Order: yaml > json > md
     *
     * @return string|null
     * */
    public function findWithoutFileExtension(string $directory, string $fileName) : ?string
    {
        $fileExtensions = ["yaml", "yml", "json", "md", "markdown"];

        try {
            $files = $this->filesystem()->listDirectory($directory);
        } catch (FileNotFoundException $e) {
            return null;
        }

        $files = array_filter(
            $files,
            function($value, $filePath) use ($fileName) {
                return !is_array($value) && pathinfo($filePath, PATHINFO_FILENAME) === $fileName;
            },
            ARRAY_FILTER_USE_BOTH
        );

        foreach ($fileExtensions as $fileExtension) {
            $matchingFile = "$fileName.$fileExtension";

            if (array_key_exists($matchingFile, $files)) {
                return $files[$matchingFile];
            }
        }

        return null;
    }

    // TODO: deprecated
    /**
     * @return an array of files where the key is the relative and the value is the absolute path.
     * @throws FileNotFoundException
     * */
    public function listDirectory(string $path, bool $recursive = false) : array
    {
        if ($recursive) {
            return $this->filesystem()->listDirectoryRecursive($path);
        }

        return $this->filesystem()->listDirectory($path);
    }

    public function cache() : CacheModule
    {
        if ($this->cacheModule === null) {
            $this->cacheModule = new CacheModule($this);
        }

        return $this->cacheModule;
    }

    public function config() : ConfigModule
    {
        if ($this->configModule === null) {
            $this->configModule = new ConfigModule($this);
        }

        return $this->configModule;
    }

    public function content() : ContentModule
    {
        if ($this->contentModule === null) {
            $this->contentModule = new ContentModule($this);
        }

        return $this->contentModule;
    }

    public function download() : DownloadModule
    {
        if ($this->downloadModule === null) {
            $this->downloadModule = new DownloadModule($this);
        }

        return $this->downloadModule;
    }

    public function filesystem() : CoreFilesystem
    {
        return $this->filesystem;
    }

    public function posts() : PostModule
    {
        if ($this->postModule === null) {
            $this->postModule = new PostModule($this);
        }

        return $this->postModule;
    }

    public function route() : RouteModule
    {
        if ($this->routeModule === null) {
            $this->routeModule = new RouteModule($this);
        }

        return $this->routeModule;
    }

    public function runtime() : PhpRuntime
    {
        return $this->phpRuntime;
    }

    public function view() : ViewModule
    {
        if ($this->viewModule === null) {
            $this->viewModule = new ViewModule($this);
        }

        return $this->viewModule;
    }

    public function setRuntime($phpRuntime)
    {
        $this->phpRuntime = $phpRuntime;
    }
}
