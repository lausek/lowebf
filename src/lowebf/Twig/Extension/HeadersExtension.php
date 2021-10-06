<?php

namespace lowebf\Twig\Extension;

use lowebf\Environment;
use lowebf\Twig\Extension\HelperExtension;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Formatter\Crunched;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class HeadersExtension extends AbstractExtension
{
    /** @var int */
    const MAX_TITLE_LENGTH = 35;

    /** @var int */
    const MAX_DESCRIPTION_LENGTH = 65;

    /** @var Environment */
    private $env = null;

    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction("linkPreviewHeaders", [$this, "writeLinkPreviewHeaders"])
        ];
    }

    public function writeLinkPreviewHeaders($args)
    {
        $defaultOptions = [
            "previewImageUrl" => "",
            "url" => "",
            "title" => "",
            "description" => "",
            "type" => "article",
            "locale" => "en_US",
        ];
        $options = array_merge($defaultOptions, $args);

        $helperExtension = new HelperExtension($this->env);

        $options["title"] = $helperExtension->getTrimmedString($options["title"], self::MAX_TITLE_LENGTH);
        $options["description"] = $helperExtension->getTrimmedString($options["description"], self::MAX_DESCRIPTION_LENGTH);

        $options = (object)$options;

        $headers = "";
        $headers .= "<meta property='og:image' content='$options->previewImageUrl'>";
        $headers .= "<meta property='og:title' content='$options->title'>";
        $headers .= "<meta property='og:url' content='$options->url'>";
        $headers .= "<meta property='og:description' content='$options->description'>";
        $headers .= "<meta property='og:type' content='$options->type'>";
        $headers .= "<meta property='og:locale' content='$options->locale'>";

        $this->env->runtime()->writeOutput($headers);
    }
}
