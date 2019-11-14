<?php

namespace lowebf;

use Twig\Loader;
use Twig\TwigFunction;
use Twig\Extension\DebugExtension;
use Twig\Extension\ProfilerExtension;
use Twig\Profiler\Profile;

class Environment {
    public static $instance;
    public $data;
    public $twig;
    public $profile;


    public static function getInstance(): Environment
    {
        if(self::$instance === null) {
            self::$instance = Environment::create();
        }
        return self::$instance;
    }

    public static function create(string $opt_path = null): Environment
    {
        return new Environment($opt_path);
    }

    private function __construct(string $opt_path = null)
    {
        $path = $opt_path ?? $this->getRoot();
        $template_path = $this->asAbsolutePath('/site/resources/template/');
        $loader = new Loader\FilesystemLoader($template_path);

        $this->data = new DataProvider("$path/data");
        $this->twig = new \Twig\Environment($loader, $this->data->config->getTwig());
        $this->profile = new Profile();

        $this->twig->addFunction(Extension\Stylesheet::new($this->twig->getCache()));

        if ($this->twig->isDebug())
        {
            $this->twig->addExtension(new ProfilerExtension($this->profile));

            $this->twig->addExtension(new DebugExtension());
        }
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getPostAttributes()
    {
        return $_POST;
    }

    public function getGetAttributes()
    {
        return $_GET;
    }

    public function asAbsolutePath(string $suffix): string
    {
        $prefix = $this->getRoot();
        return $prefix . $suffix;
    }

    public function asAbsoluteUrl(string $suffix): string
    {
        return $suffix;
    }

    public function getRoot(): string
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }
}
