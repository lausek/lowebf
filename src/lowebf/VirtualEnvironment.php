<?php

namespace lowebf;

use lowebf\Error\FileNotFoundException;
use lowebf\Filesystem\VirtualFilesystem;
use lowebf\Module\GlobalsModule;
use lowebf\PhpRuntime;

class VirtualPhpRuntime extends PhpRuntime
{
    public function exitRuntime() {
        // avoid exit being called while testing
    }
}

class VirtualEnvironment extends Environment
{
    public $globals;

    public function __construct(string $dir = "/ve")
    {
        parent::__construct($dir);

        $this->filesystem = new VirtualFilesystem($this);
        $this->phpRuntime = new VirtualPhpRuntime();

        $this->globals = [
            "get" => [],
            "post" => [],
            "server" => [],
        ];
        $this->globalsModule = new class($this) extends GlobalsModule {
            public function __construct($env)
            {
                parent::__construct($env);

                $this->getGlobals = &$env->globals["get"];
                $this->postGlobals = &$env->globals["post"];
                $this->serverGlobals = &$env->globals["server"];
            }
        };
    }

    public function setGetGlobal(string $key, $value)
    {
        $this->globals["get"][$key] = $value;
    }

    public function setPostGlobal(string $key, $value)
    {
        $this->globals["post"][$key] = $value;
    }

    public function setServerGlobal(string $key, $value)
    {
        $this->globals["server"][$key] = $value;
    }
}
