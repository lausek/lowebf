<?php

namespace lowebf\Twig\Extension;

use lowebf\Environment;

use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

final class HelperExtension extends AbstractExtension
{
    /** @var Environment */
    private $env = null;

    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    public function getTests()
    {
        return [
            new TwigTest(
                "array",
                function($obj) {
                    return is_array($obj);
                }
            ),
        ];
    }
}
