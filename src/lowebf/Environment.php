<?php

namespace lowebf;

use lowebf\Module\CacheModule;
use lowebf\Module\ConfigModule;
use lowebf\Module\ContentModule;
use lowebf\Module\DownloadModule;
use lowebf\Module\PostModule;
use lowebf\Module\ViewModule;

class Environment {

    /* @var PhpRuntime */
	protected $phpRuntime = null;
    /* @var string */
	protected $rootPath;
    /* @var string */
	protected $dataPath;

    /* @var CacheModule */
	protected $cacheModule = null;
    /* @var ConfigModule */
	protected $configModule = null;
    /* @var ContentModule */
	protected $contentModule = null;
    /* @var DownloadModule */
	protected $downloadModule = null;
    /* @var PostModule */
	protected $postModule = null;
    /* @var ViewModule */
	protected $viewModule = null;

    public static function getInstance(): Environment {
        return new Environment($_SERVER["DOCUMENT_ROOT"]);
    }

    public function __construct(string $rootPath, string $dataPath = null) {
        // TODO: also trim backslash '\'
        $rootPath = rtrim($rootPath, "/");

        if($dataPath === null) {
            $dataPath = "$rootPath/data";
        }

        $this->rootPath = $rootPath;
        $this->dataPath = $dataPath;

	    $this->phpRuntime = new PhpRuntime($this);
    }

    public function asAbsolutePath(string $subpath): string {
        $left = rtrim($this->getRootPath(), "/");
        $right = ltrim($subpath, "/");
        $path = realpath("$left/$right");

        if($path === false) {
            throw new \Exception();
        }

        return $path;
    }

    public function asAbsoluteDataPath(string $subpath): string {
        $left = rtrim($this->getDataPath(), "/");
        $right = ltrim($subpath, "/");
        $joined = "$left/$right";
        $path = realpath($joined);

        if($path === false) {
            throw new \Exception("cannot create realpath '$joined': path does not exist.");
        }

        return $path;
    }

    public function getRootPath(): string {
        return $this->rootPath;
    }

    public function getDataPath(): string {
        return $this->dataPath;
    }

    public function loadFile(string $path): ?mixed {

    }

    public function saveFile(string $path, mixed $content) {

    }

    public function listDirectory(string $path): array {
    }

    public function cache(): CacheModule {
        if($this->cacheModule === null) {
	        $this->cacheModule = new CacheModule($this);
        }

        return $this->cacheModule;
    }

    public function config(): ConfigModule {
        if($this->configModule === null) {
	        $this->configModule = new ConfigModule($this);
        }

        return $this->configModule;
    }

    public function content(): ContentModule {
        if($this->contentModule === null) {
	        $this->contentModule = new ContentModule($this);
        }

        return $this->contentModule;
    }

    public function download(): DownloadModule {
        if($this->downloadModule === null) {
	        $this->downloadModule = new DownloadModule($this);
        }

        return $this->downloadModule;
    }

    public function posts(): PostModule {
        if($this->postModule === null) {
	        $this->postModule = new PostModule($this);
        }

        return $this->postModule;
    }

    public function runtime(): PhpRuntime {
        return $this->phpRuntime;
    }

    public function view(): ViewModule {
        if($this->viewModule === null) {
	        $this->viewModule = new ViewModule($this);
        }

        return $this->viewModule;
    }
}
