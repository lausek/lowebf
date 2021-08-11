<?php

namespace lowebf;

use lowebf\Module\PostModule;
use lowebf\Module\ViewModule;
use lowebf\Module\CacheModule;
use lowebf\Module\DownloadModule;
use lowebf\Module\ContentModule;
use lowebf\Module\CacheModule;
use lowebf\Module\ConfigModule;
use lowebf\Module\ContentModule;
use lowebf\Module\DownloadModule;
use lowebf\Module\PostModule;
use lowebf\Module\ViewModule;

class Environment {

    /* @var PhpRuntime */
	private $phpRuntime = null;
    /* @var string */
	private $rootPath;
    /* @var string */
	private $dataPath;

    /* @var CacheModule */
	private $cacheModule = null;
    /* @var ConfigModule */
	private $configModule = null;
    /* @var ContentModule */
	private $contentModule = null;
    /* @var DownloadModule */
	private $downloadModule = null;
    /* @var PostModule */
	private $postModule = null;
    /* @var ViewModule */
	private $viewModule = null;

    public static function getInstance(): Environment {
        $rootPath = rtrim($_SERVER["DOCUMENT_ROOT"], "/");
        $dataPath = "$rootPath/data";

        return new Environment($rootPath, $dataPath);
    }

    public function __construct(string $rootPath, string $dataPath) {
        $this->rootPath = $rootPath;
        $this->dataPath = $dataPath;

	    $this->phpRuntime = new PhpRuntime($this);
	    $this->cacheModule = new CacheModule($this);
	    $this->configModule = new ConfigModule($this);
	    $this->contentModule = new ContentModule($this);
	    $this->downloadModule = new DownloadModule($this);
	    $this->postModule = new PostModule($this);
	    $this->viewModule = new ViewModule($this);
    }

	public function asAbsolutePath(string $subpath): ?string {}

	public function asAbsoluteDataPath(string $subpath): ?string {}

	public function loadFile(string $path): ?mixed {}

	public function saveFile(string $path, mixed $content) {}

	public function list(string $path): ?array {}

	public function cache(): CacheModule {}

	public function config(): ConfigModule {}

	public function content(): ContentModule {}

	public function download(): DownloadModule {}

	public function posts(): PostModule {}

	public function runtime(): PhpRuntime {}

	public function view(): ViewModule {}
}
