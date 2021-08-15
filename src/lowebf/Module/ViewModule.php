<?php

namespace lowebf\Module;

use lowebf\Environment;

class ViewModule extends Module
{
    /** @var Twig */
	    protected $twig;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->twig = null;
    }

    public function render(string $template, array $data) {}

    public function renderToString(string $template, array $data) : string {}
}
