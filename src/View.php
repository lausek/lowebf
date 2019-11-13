<?php

namespace lowebf;

use Twig\Loader;
use Twig\TwigFunction;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extension\ProfilerExtension;
use Twig\Profiler\Profile;
use Twig\Profiler\Dumper\HtmlDumper;

use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Crunched;

final class View
{

    private function __construct()
    {
        $loader = new Loader\FilesystemLoader('resources/template/');

        $this->twig = new Environment($loader, [
            'debug' => true,
            //'cache' => Config::getRoot() . '/cache/twig',
        ]);

        $cache = $this->twig->getCache();

        $this->twig->addFunction(Extension\Stylesheet::new($cache));

        if ($this->twig->isDebug())
        {
            $this->profile = new Profile();
            $this->twig->addExtension(new ProfilerExtension($this->profile));

            $this->twig->addExtension(new DebugExtension());
        }
    }

    public static function new()
    {
        return new View();
    }

    public function renderPartially(string $filePath, array $args=[]): string
    {
        $template = $this->twig->load($filePath);
        return $template->render([
            'data' => $args,
            'config' => Config::new(),
            'get' => $_GET,
            'post' => $_POST,
        ]);
    }

    public function render(string $filePath, array $args=[])
    {

        $type = pathinfo($filePath, PATHINFO_EXTENSION);

        switch ($type)
        {
            case 'html':
                header('Content-Type: text/html');
                break;
            case 'xml':
                header('Content-Type: text/xml');
                break;
        }

        if ($type === 'html' && $this->twig->isDebug())
        {
            echo '<b style="color:red;">DEBUG IS ACTIVE!</b>';
        }

        echo $this->renderPartially($filePath, $args);

        if ($type === 'html' && $this->twig->isDebug())
        {
            echo (new HtmlDumper)->dump($this->profile);
        }

        exit;
    }

}
