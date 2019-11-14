<?php

namespace lowebf;

use Twig\Loader;
use Twig\TwigFunction;
use Twig\Extension\DebugExtension;
use Twig\Extension\ProfilerExtension;
use Twig\Profiler\Profile;
use Twig\Profiler\Dumper\HtmlDumper;

use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Crunched;

final class View
{
    private $_env;
    private $_profile;
    private $_twig;

    private function __construct()
    {
        $this->_env = Environment::getInstance();

        $template_path = $this->_env->asAbsolutePath('/site/resources/template/');
        $loader = new Loader\FilesystemLoader($template_path);
        $this->_twig = new \Twig\Environment($loader, $this->_env->data->getTwigSettings());

        $this->_twig->addFunction(Extension\Stylesheet::new($this->_twig->getCache()));

        if ($this->_twig->isDebug())
        {
            $this->_profile = new Profile();
            $this->_twig->addExtension(new ProfilerExtension($this->_profile));

            $this->_twig->addExtension(new DebugExtension());
        }
    }

    public static function new()
    {
        return new View();
    }

    public function renderPartially(string $filePath, array $args=[]): string
    {
        $template = $this->_twig->load($filePath);
        return $template->render([
            'data' => $args,
            'config' => $this->_env->data->config,
            'env' => $this->_env,
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

        if ($type === 'html' && $this->_twig->isDebug())
        {
            echo '<div display="block" style="border: 5px solid red; padding: 5px;">';
            echo '<b style="color:red;">DEBUG IS ACTIVE!</b>';
            echo '<hr />';
            echo (new HtmlDumper)->dump($this->_profile);
            echo '</div>';
        }

        echo $this->renderPartially($filePath, $args);

        exit;
    }

}
