<?php

namespace lowebf\Module;

use lowebf\Environment;
use lowebf\Twig\Cache;
use lowebf\Twig\Extension\HeadersExtension;
use lowebf\Twig\Extension\StylesheetExtension;
use lowebf\Twig\Extension\UrlExtension;
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
        $twigOptions["debug"] = $env->config()->isDebugEnabled();

        // Environment implements the Twig LoaderInterface
        $loader = new TemplateLoader($env);
        $this->twig = new \Twig\Environment($loader, $twigOptions);

        $this->twig->addExtension(new HeadersExtension($env));
        $this->twig->addExtension(new StylesheetExtension($env));
        $this->twig->addExtension(new UrlExtension($env));
    }

    public function getTwigEnvironment() : \Twig\Environment
    {
        return $this->twig;
    }

    public function render(string $templatePath, $data = [])
    {
        $templateAbsolutePath = $this->env->asAbsolutePath("site/template/$templatePath");
        $output = $this->renderToString($templatePath, $data);

        $this->env->runtime()->setContentTypeFromFile($templateAbsolutePath);
        $this->env->runtime()->writeOutput($output);
        $this->env->runtime()->exit();
    }

    public function renderToString(string $templatePath, $data = []) : string
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
