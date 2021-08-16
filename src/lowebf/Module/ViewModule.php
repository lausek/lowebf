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

        if ($env->config()->get("chachingEnabled")) {
            $twigOptions["cache"] = null;
        }

        $this->twig = new \Twig\Environment($loader, $options);
    }

    public function render(string $template, array $data) {}

    public function renderToString(string $template, array $data) : string {}
}
