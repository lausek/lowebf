<?php

namespace lowebf\Twig\Extension;

use lowebf\Environment;

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
            new TwigFunction("resourceUrl", [$this, "formatResourceUrl"]),
            new TwigFunction("resourceAbsoluteUrl", [$this, "formatResourceAbsoluteUrl"]),
            new TwigFunction("absoluteUrl", [$this, "formatAbsoluteUrl"]),
            new TwigFunction("url", [$this, "formatUrl"]),
        ];
    }

    public function formatAbsoluteUrl(string $path, $args = null) : string
    {
        $protocol = isset($_SERVER["PROTOCOL"]) ? $_SERVER["PROTOCOL"] : "https";
        $host = isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : "localhost";
        $path = $this->formatUrl($path, $args);

        return "$protocol://$host$path";
    }

    public function formatUrl(string $path, $args = null) : string
    {
        $urlArguments = [];
        $urlArgumentString = "";

        if ($args !== null) {
            foreach ($args as $key => $value) {
                $key = urlencode($key);
                $value = urlencode($value);
                $urlArguments[] = "$key=$value";
            }

            $urlArgumentString = "?" . implode("&", $urlArguments);
        }

        return "/$path$urlArgumentString";
    }

    public function formatResourceUrl(string $path) : string
    {
        return $this->env->route()->urlFor($path);
    }

    public function formatResourceAbsoluteUrl(string $path) : string
    {
        return $this->env->route()->absoluteUrlFor($path);
    }
}
