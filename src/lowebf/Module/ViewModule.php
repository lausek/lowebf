<?php

namespace lowebf\Module;

use lowebf\Environment;
use lowebf\Twig\Cache;
use lowebf\Twig\Extension\StylesheetExtension;
use lowebf\Twig\TemplateLoader;

class ViewModule extends Module
{
    /** @var \Twig\Environment */
	    protected $twig;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $twigOptions = [];

        $twigOptions["cache"] = $env->config()->isCacheEnabled() ? new Cache($env) : false;

        // Environment implements the Twig LoaderInterface
        $loader = new TemplateLoader($env);
        $this->twig = new \Twig\Environment($loader, $twigOptions);

        $this->twig->addExtension(new StylesheetExtension($env));
    }

    public function render(string $templatePath, array $data = [])
    {
        $output = $this->renderToString($templatePath, $data);

        $this->env->runtime()->setContentTypeFromFile($templatePath);
        $this->env->runtime()->writeOutput($output);
        $this->env->runtime()->exit();
    }

    public function renderToString(string $templatePath, array $data = []) : string
    {
        $template = $this->twig->load($templatePath);

        return $template->render(
            [
                "data" => $data,
                "config" => $this->env->config(),
                "get" => $_GET,
                "post" => $_POST,
                "server" => $_SERVER,
            ]
        );
    }
}
