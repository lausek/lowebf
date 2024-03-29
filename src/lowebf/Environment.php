<?php

namespace lowebf;

use lowebf\Filesystem\CoreFilesystem;
use lowebf\Filesystem\Filesystem;
use lowebf\Error\FileNotFoundException;
use lowebf\Module\CacheModule;
use lowebf\Module\ConfigModule;
use lowebf\Module\ContentModule;
use lowebf\Module\DownloadModule;
use lowebf\Module\GalleryModule;
use lowebf\Module\GlobalsModule;
use lowebf\Module\PostModule;
use lowebf\Module\RouteModule;
use lowebf\Module\ThumbnailModule;
use lowebf\Module\ViewModule;
use lowebf\Result;

function getFileType(string $path) : string
{
    $extension = pathinfo($path, PATHINFO_EXTENSION);
    $extension = mb_strtolower($extension);

    switch ($extension) {
        case "jpg":
            return "jpeg";
    }

    return $extension;
}

function extractAttributesFromPath(string $path) : array
{
    $fileName = pathinfo($path, PATHINFO_FILENAME);
    $date = substr($fileName, 0, 10);

    $title = substr($fileName, 11);
    $title = str_replace("-", " ", $title);
    $title = ucwords($title);

    return [
        "date" => $date,
        "title" => $title,
    ];
}

class Environment
{
    /** @var PhpRuntime */
	    protected $phpRuntime;
    /** @var string */
	    protected $rootPath;
    /** @var string */
	    protected $dataPath;
    /** @var CoreFilesystem */
    protected $filesystem;

    /** @var CacheModule|null */
	    protected $cacheModule = null;
    /** @var ConfigModule|null */
	    protected $configModule = null;
    /** @var ContentModule|null */
	    protected $contentModule = null;
    /** @var DownloadModule|null */
	    protected $downloadModule = null;
    /** @var GalleryModule|null */
	    protected $galleryModule = null;
    /** @var GlobalsModule|null */
	    protected $globalsModule = null;
    /** @var PostModule|null */
	    protected $postModule = null;
    /** @var RouteModule|null */
	    protected $routeModule = null;
    /** @var ThumbnailModule|null */
	    protected $thumbnailModule = null;
    /** @var ViewModule|null */
	    protected $viewModule = null;

    public static function getInstance() : Environment
    {
        $rootDirectory = ini_get("lowebf_root");

        if ($rootDirectory === false) {
            $rootDirectory = $_SERVER["DOCUMENT_ROOT"];
        }

        return new Environment($rootDirectory);
    }

    public function __construct(string $rootPath, string $dataPath = null)
    {
        // TODO: also trim backslash '\'
        $rootPath = rtrim($rootPath, "/");

        // find last occurrence index of /site/public
        $sitePathOffset = strrpos($rootPath, "/site/public");
        if ($sitePathOffset !== false) {
            $rootPath = substr($rootPath, 0, $sitePathOffset);
        }

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

    public function isAbsolutePath(string $path) : bool
    {
        // check if path starts with root
        return strpos($this->getRootPath(), $path) === 0;
    }

    public function asRelativePath(string $path) : string
    {
        $rootPath = $this->env->getRootPath();
        $rootPathLen = strlen($rootPath);

        // path is already relative
        if (!$this->isAbsolutePath($path) || strlen($path) < $rootPathLen) {
            return $path;
        }

        return substr($path, $rootPathLen);
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
    /**
     * @return Result<string>
     * */
    public function loadFile(string $path) : Result
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
     * @return Result<string>
     * */
    public function findWithoutFileExtension(string $directory, string $fileName) : Result
    {
        $fileExtensions = ["yaml", "yml", "json", "md", "markdown"];

        $result = $this->filesystem()->listDirectory($directory);
        if ($result->isError()) {
            return $result;
        }

        $files = array_filter(
            $result->unwrap(),
            function($value, $filePath) use ($fileName) {
                return !is_array($value) && pathinfo($filePath, PATHINFO_FILENAME) === $fileName;
            },
            ARRAY_FILTER_USE_BOTH
        );

        foreach ($fileExtensions as $fileExtension) {
            $matchingFile = "$fileName.$fileExtension";

            if (array_key_exists($matchingFile, $files)) {
                return Result::ok($files[$matchingFile]);
            }
        }

        return Result::error(new FileNotFoundException("$fileName in $directory"));
    }

    // TODO: deprecated
    /**
     * @return Result<array> an array of files where the key is the relative and the value is the absolute path.
     * @throws FileNotFoundException
     * */
    public function listDirectory(string $path) : Result
    {
        return $this->filesystem()->listDirectory($path);
    }

    /**
     * @return Result<array>
     * */
    public function listDirectoryRecursive(string $path, int $depth = PHP_INT_MAX) : Result
    {
        return $this->filesystem()->listDirectoryRecursive($path, $depth);
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

    public function galleries() : GalleryModule
    {
        if ($this->galleryModule === null) {
            $this->galleryModule = new GalleryModule($this);
        }

        return $this->galleryModule;
    }

    public function globals() : GlobalsModule
    {
        if ($this->globalsModule === null) {
            $this->globalsModule = new GlobalsModule($this);
        }

        return $this->globalsModule;
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

    public function thumbnail() : ThumbnailModule
    {
        if ($this->thumbnailModule === null) {
            $this->thumbnailModule = new ThumbnailModule($this);
        }

        return $this->thumbnailModule;
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

    public function setFilesystem($filesystem)
    {
        $this->filesystem = $filesystem;
    }
}
