<?php

namespace lowebf\Module;

use lowebf\Environment;

class ViewModule extends Module
{
    /** @var \Twig\Environment */
	    protected $twig;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $twigOptions = [];

        if ($env->config()->isCacheEnabled()) {
            $twigOptions["cache"] = null;
        }

        $this->twig = new \Twig\Environment($loader, $options);
    }

    public function render(string $templatePath, array $data)
    {
        $output = $this->renderToString($templatePath, $data);

        $fileExtension = pathinfo($templatePath, PATHINFO_EXTENSION);

        $this->env->runtime()->writeOutput($output);
        $this->env->runtime()->exit();
    }

    public function renderToString(string $templatePath, array $data) : string
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
