<?php

namespace lowebf;

use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Crunched;
use Twig\Profiler\Dumper\HtmlDumper;

final class View
{
    private $_env;

    private function __construct()
    {
        $this->_env = Environment::getInstance();
    }

    public static function new()
    {
        return new View();
    }

    public function renderPartially(string $filePath, array $args=[]): string
    {
        $template = $this->_env->twig->load($filePath);
        return $template->render([
            'data' => $args,
            'config' => $this->_env->data->config,
            'env' => $this->_env,
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

        if ($type === 'html' && $this->_env->twig->isDebug())
        {
            echo '<div display="block" style="border: 5px solid red; padding: 5px;">';
            echo '<b style="color:red;">DEBUG IS ACTIVE!</b>';
            echo '<hr />';
            echo (new HtmlDumper)->dump($this->_env->profile);
            echo '</div>';
        }

        echo $this->renderPartially($filePath, $args);

        exit;
    }

}
