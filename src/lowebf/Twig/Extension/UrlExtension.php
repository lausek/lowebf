<?php

namespace lowebf\Twig\Extension;

use lowebf\Environment;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Formatter\Crunched;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class UrlExtension extends AbstractExtension
{
    /** @var Environment */
    private $env = null;

    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction("url", [$this, "formatUrl"])
        ];
    }

    public function formatUrl(string $path, $args = null)
    {
        $urlArguments = [];
        $urlArgumentString = "";

        if ($args !== null) {
            foreach ($args as $key => $value) {
                $key = urlencode($key);
                $value = urlencode($value);
                $urlArguments[] = "$key=$value";
            }

            $urlArgumentString = "?" . implode($urlArguments, "&");
        }

        return "/$path$urlArgumentString";
    }
}
